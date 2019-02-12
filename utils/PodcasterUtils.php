<?php
namespace Plugin\Podcaster;

class PodcasterUtils {

    private $rssFeed;
    private $episode;

    public function __construct($page) {
        $this->rssFeed = $page;
        return true;
    }

    public function getEpisodes() {
        return $this->rssFeed->podcasterSource()->toPage()->children()->listed()->filterBy('date', '<=', time())->filter(function($child) {
            return $child->hasAudio();
        });
    }

    public function setCurrentEpisode($episode) {
        $this->episode = $episode;
    }

    public function getGuid() {
        $audio = $this->getPodcastFile();
        return $audio->guid();
    }

    public function getAudioEnclosures($episode): string {

        $xmlOutput = [];
        $audio = $this->getPodcastFile();

        $audioUrl = $episode->url() . '/' . option('mauricerenck.podcaster.downloadTriggerPath') . '/' . $audio->filename();
        $xmlOutput[] = '<enclosure url="' . $audioUrl .'" length="' . $audio->size() . '" type="audio/mpeg"/>';

        return implode("\n", $xmlOutput);
    }

    public function getAudioDuration() {
        $audio = $this->getPodcastFile();
        return $audio->duration();
    }


    public function printItunesCategories() {
        $output = [];
        foreach ($this->parseItunesCategories() as $mainCategory => $subCategories) {

            $output[] = '<itunes:category text="' . htmlentities($mainCategory) . '">';
            foreach($subCategories as $subCategory) {
                $output[] = '<itunes:category text="' . htmlentities($subCategory) . '"/>';
            }
            $output[] = '</itunes:category>';
        }

        echo implode("", $output);
    }

    public function getCoverImage() {
        if($this->rssFeed->podcasterCover()->isNotEmpty()) {
            $output = '<image>';
            $output .= '<url>' . $this->rssFeed->podcasterCover()->toFile()->url() . '</url>';
            $output .= '<title>' . $this->rssFeed->podcasterTitle() . '</title>';
            $output .= '<link><![CDATA[' . $this->rssFeed->podcasterLink() . ']]></link>';
            $output .= '</image>';
            $output .= '<itunes:image href="' . $this->rssFeed->podcasterCover()->toFile()->url() .'"/>';

            echo $output;
        }
    }

    public function printFieldValue(string $source, string $xmlTag, string $blueprintField, bool $useCData = false) {
        if ($this->$source->$blueprintField()->isNotEmpty()) {
            if($useCData) {
                $value = '<![CDATA[' . $this->$source->$blueprintField()->kirbytext() . ']]>';
            } else {
                $value = $this->$source->$blueprintField();
            }

            echo '<' . $xmlTag . '>' . $value . '</' . $xmlTag . '>' . "\n";
        }
    }

    public function printBoolValue(string $source, string $xmlTag, string $blueprintField) {
        if ($this->$source->$blueprintField()->isNotEmpty()) {
            echo '<' . $xmlTag . '>' . (($this->$source->$blueprintField()->isTrue()) ? 'yes' : 'no') . '</' . $xmlTag . '>' . "\n";
        }
    }


    public function getChapters() {
        if ($this->episode->podcasterChapters()->isEmpty()) {
            return false;
        }

        $chapterList = [];

        foreach($this->episode->podcasterChapters()->toStructure() as $chapter) {

            $newChapter = ['<psc:chapter'];
            $newChapter[] = 'start="' . $chapter->podcasterChapterTimestamp() . '"';
            $newChapter[] = 'title="' . $chapter->podcasterChapterTitle() . '"';

            if($chapter->podcasterChapterUrl()->isNotEmpty()) {
                $newChapter[] = 'href="' . $chapter->podcasterChapterUrl() . '"';
            }

            if($chapter->podcasterChapterImage()->isNotEmpty()) {
                $newChapter[] = 'image="' . $chapter->podcasterChapterImage()->toFile()->url() . '"';
            }

            $newChapter[] = '/>';
            $chapterList[] = implode(' ', $newChapter);
        }

        if(count($chapterList) > 0) {
            return '<psc:chapters version="1.2" xmlns:psc="http://podlove.org/simple-chapters">' . implode("\n", $chapterList) . '</psc:chapters>';
        }
    }

    public function getPodcastFile() {
        return $this->episode->audio($this->episode->podcasterMp3())->first();
    }

    private function parseItunesCategories(): array {
        $categories = [];
        foreach ($this->rssFeed->podcasterCategories()->toStructure() as $category) {
            $currentCategories = explode('/', $category->podcasterMainCategory());
            if (!isset($categories[$currentCategories[0]])) {
                $categories[$currentCategories[0]] = [];
            }

            for ($i = 1; $i < count($currentCategories); $i++) {
                $categories[$currentCategories[0]][] = $currentCategories[$i];
            }
        }

        return $categories;
    }

}
