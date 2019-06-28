<?php

namespace Plugin\Podcaster;

class PodcasterAudioUtils
{
    public function __construct()
    {
        return true;
    }

    public function setAudioFileMeta($id3, $file)
    {
        $time = round($id3['playtime_seconds']);

        $duration = $this->parseAudioDuration($time);
        $title = $this->parseTitle($id3);
        $this->getChapters($id3);
        $this->writeAudioFileMeta($file, $title, $duration);
    }

    public function setAudioMetaToPage($id3, $file)
    {
        $newPageData = [];
        $page = $file->page();

        $newPageData['podcasterTitle'] = ($this->getId3Tag('title', $id3)) ? $this->getId3Tag('title', $id3) : null;
        $newPageData['podcasterEpisode'] = ($this->getId3Tag('track_number', $id3)) ? $this->getId3Tag('track_number', $id3) : null;
        $newPageData['podcasterDescription'] = ($this->getId3Tag('comment', $id3)) ? $this->getId3Tag('comment', $id3) : null;
        $newPageData['podcasterSubtitle'] = ($this->getId3Tag('subtitle', $id3)) ? $this->getId3Tag('subtitle', $id3) : null;

        $fielData = [];
        $chapters = $this->getChapters($id3);

        if ($chapters !== null) {
            foreach ($chapters as $chapter) {
                $timestamp = $this->parseAudioDuration($chapter['time_begin'] / 1000);
                $fieldData[] = [
                    'podcasterchaptertimestamp' => $timestamp,
                    'podcasterchaptertitle' => ($chapter['chapter_name']) ? $chapter['chapter_name'] : null,
                    'podcasterchapterurl' => (isset($chapter['chapter_url']['chapter url'])) ? $chapter['chapter_url']['chapter url'] : null,
                    'podcasterchapterimage' => []
                ];
            }

            $fieldData = \yaml::encode($fieldData);
            $newPageData['podcasterchapters'] = $fieldData;
        }

        $page->update($newPageData);
    }

    public function getAudioMeta($file)
    {
        $path = $file->root();

        $id3Parser = new \getID3();
        $id3 = $id3Parser->analyze($path);
        return $id3;
    }

    protected function parseAudioDuration($seconds)
    {
        return sprintf('%02d:%02d:%02d', ($seconds / 3600), ($seconds / 60 % 60), $seconds % 60);
    }

    protected function parseTitle($id3)
    {
        return $id3['tags_html']['id3v2']['title'][0];
    }

    protected function getId3Tag($tag, $id3)
    {
        return (isset($id3['tags']['id3v2'][$tag][0])) ? $id3['tags']['id3v2'][$tag][0] : null;
    }

    protected function getChapters($id3)
    {
        return (isset($id3['id3v2']['chapters'])) ? $id3['id3v2']['chapters'] : null;
    }

    protected function writeAudioFileMeta($file, $title, $duration)
    {
        $file->update([
            'episodeTitle' => $title,
            'duration' => $duration,
            'guid' => md5(time())
        ]);
    }
}
