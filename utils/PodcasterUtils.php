<?php

namespace Plugin\Podcaster;

use \Kirby\Toolkit\Xml;

class PodcasterUtils
{
    private $rssFeed;
    private $episode;

    public function setFeed($page)
    {
        $this->rssFeed = $page;
        return true;
    }

    public function getEpisodes()
    {
        return $this->rssFeed->podcasterSource()
            ->toPage()
            ->children()
            ->listed()
            ->filter(function ($child) {
                return $child->date()->toDate() <= time();
            })
            ->filter(function ($child) {
                return $child->hasAudio();
            })
            ->sortBy('date', 'desc');
    }

    public function setCurrentEpisode($episode)
    {
        $this->episode = $episode;
    }

    public function getGuid()
    {
        $audio = $this->getPodcastFile();
        return $audio->guid();
    }

    public function getAudioEnclosures($episode): string
    {
        $xmlOutput = [];
        $audio = $this->getPodcastFile();

        $audioUrl = $episode->url() . '/' . option('mauricerenck.podcaster.downloadTriggerPath', 'download') . '/' . $audio->filename();
        $xmlOutput[] = '<enclosure url="' . $audioUrl . '" length="' . $audio->size() . '" type="audio/mpeg"/>';

        return implode("\n", $xmlOutput);
    }

    public function getAudioDuration()
    {
        $audio = $this->getPodcastFile();
        return $audio->duration();
    }

    public function printItunesCategories()
    {
        $output = [];
        foreach ($this->parseItunesCategories() as $mainCategory => $subCategories) {
            $output[] = '<itunes:category text="' . Xml::encode($mainCategory) . '">';
            foreach ($subCategories as $subCategory) {
                $output[] = '<itunes:category text="' . Xml::encode($subCategory) . '"/>';
            }
            $output[] = '</itunes:category>';
        }

        echo implode('', $output);
    }

    public function getCoverImage()
    {
        if ($this->rssFeed->podcasterCover()->isNotEmpty() && !is_null($this->rssFeed->podcasterCover()->toFile())) {
            $output = '<image>';
            $output .= '<url>' . Xml::encode($this->rssFeed->podcasterCover()->toFile()->url()) . '</url>';
            $output .= '<title>' . Xml::encode($this->rssFeed->podcasterTitle()) . '</title>';
            $output .= '<link><![CDATA[' . Xml::encode($this->rssFeed->podcasterLink()) . ']]></link>';
            $output .= '</image>';

            $output .= '<itunes:image href="' . Xml::encode($this->rssFeed->podcasterCover()->toFile()->url()) . '" />';
            $output .= '<googleplay:image href="' . Xml::encode($this->rssFeed->podcasterCover()->toFile()->url()) . '" />';

            echo $output;
        }
    }

    public function printFieldValue(string $source, string $xmlTag, string $blueprintField, bool $useCData = false)
    {
        if ($this->$source->$blueprintField()->isNotEmpty()) {
            if ($useCData) {
                $value = '<![CDATA[' . $this->$source->$blueprintField()->kirbyTextInline() . ']]>';
            } else {
                $value = Xml::encode($this->$source->$blueprintField());
            }

            echo '<' . $xmlTag . '>' . $value . '</' . $xmlTag . '>' . "\n";
        }
    }

    public function printBoolValue(string $source, string $xmlTag, string $blueprintField)
    {
        if ($this->$source->$blueprintField()->isNotEmpty()) {
            echo '<' . $xmlTag . '>' . (($this->$source->$blueprintField()->isTrue()) ? 'yes' : 'no') . '</' . $xmlTag . '>' . "\n";
        }
    }

    public function getChapters()
    {
        if ($this->episode->podcasterChapters()->isEmpty()) {
            return false;
        }

        $chapterList = [];

        foreach ($this->episode->podcasterChapters()->toStructure() as $chapter) {
            $newChapter = ['<psc:chapter'];
            $newChapter[] = 'start="' . $chapter->podcasterChapterTimestamp() . '"';
            $newChapter[] = 'title="' . Xml::encode($chapter->podcasterChapterTitle()) . '"';

            if ($chapter->podcasterChapterUrl()->isNotEmpty()) {
                $newChapter[] = 'href="' . Xml::encode($chapter->podcasterChapterUrl()) . '"';
            }

            if ($chapter->podcasterChapterImage()->isNotEmpty()) {
                $newChapter[] = 'image="' . Xml::encode($chapter->podcasterChapterImage()->toFile()->url()) . '"';
            }

            $newChapter[] = '/>';
            $chapterList[] = implode(' ', $newChapter);
        }

        if (count($chapterList) > 0) {
            return '<psc:chapters version="1.2" xmlns:psc="http://podlove.org/simple-chapters">' . implode("\n", $chapterList) . '</psc:chapters>';
        }
    }

    public function getPodcastFile()
    {
        return $this->episode->audio($this->episode->podcasterMp3()->first())->first();
    }

    private function parseItunesCategories(): array
    {
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

    public function getPageFromSlug($slug)
    {
        $currentLanguage = kirby()->language();
        $cleanedSlug = (is_null($currentLanguage)) ? $slug : str_replace($currentLanguage . '/', '', $slug);

        return kirby()->page($cleanedSlug);
    }
}
