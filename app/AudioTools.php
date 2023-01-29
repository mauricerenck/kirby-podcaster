<?php

namespace mauricerenck\Podcaster;

use getID3;
use Kirby\Data\Yaml;
use Kirby\Filesystem\File;

class AudioTools
{
    public function parseAndWriteId3(File $audioFile, bool $updateEpisodePage): void
    {
        if ($audioFile->mime() === 'audio/mpeg') {
            return;
        }

        $id3Data = $this->getId3Data($audioFile);
        $this->writeAudioFileMeta($id3Data, $audioFile);

        if ($updateEpisodePage) {
            $this->writeAudioMetaToPage($id3Data, $audioFile);
        }
    }

    public function getId3Data($audioFile): array
    {
        $path = $audioFile->root();

        $id3Parser = new getID3();

        return $id3Parser->analyze($path);
    }

    public function writeAudioFileMeta($id3Data, $audioFile): void
    {
        $playTime = round($id3Data['playtime_seconds']);

        $duration = $this->convertAudioDuration($playTime);
        $title = $this->getId3Tag('title', $id3Data);

        $audioFile->update([
                               'episodeTitle' => $title,
                               'duration' => $duration,
                               'guid' => md5(time()),
                           ]);
    }

    public function writeAudioMetaToPage($id3, $audioFile): void
    {
        $page = $audioFile->page();

        if (is_null($page)) {
            return;
        }

        $newPageData = [
            'podcasterTitle' => $this->getId3Tag('title', $id3),
            'podcasterEpisode' => $this->getId3Tag('track_number', $id3),
            'podcasterDescription' => $this->getId3Tag('comment', $id3),
            'podcasterSubtitle' => $this->getId3Tag('subtitle', $id3),
        ];

        $chapters = $this->getChapters($id3);
        if ($chapters !== null) {
            foreach ($chapters as $chapter) {
                $timestamp = $this->convertAudioDuration($chapter['time_begin'] / 1000);
                $fieldData[] = [
                    'podcasterchaptertimestamp' => $timestamp,
                    'podcasterchaptertitle' => (isset($chapter['chapter_name'])) ? $chapter['chapter_name'] : null,
                    'podcasterchapterurl' => (isset($chapter['chapter_url']['chapter url'])) ? $chapter['chapter_url']['chapter url'] : null,
                    'podcasterchapterimage' => [],
                ];
            }

            $fieldData = yaml::encode($fieldData);
            $newPageData['podcasterchapters'] = $fieldData;
        }

        $page->update($newPageData);
    }

    public function convertAudioDuration($durationInseconds): string
    {
        $hours = floor($durationInseconds / 3600);
        $minutes = floor(round($durationInseconds / 60) % 60);
        $seconds = $durationInseconds % 60;

        return sprintf('%02d:%02d:%02d', intval($hours), $minutes, $seconds);
    }

    public function getId3Tag($tag, $id3)
    {
        return (isset($id3['tags']['id3v2'][$tag]) && isset($id3['tags']['id3v2'][$tag][0])) ? $id3['tags']['id3v2'][$tag][0] : null;
    }

    public function getChapters($id3)
    {
        return (isset($id3['id3v2']['chapters'])) ? $id3['id3v2']['chapters'] : null;
    }
}