<?php

namespace mauricerenck\Podcaster;

use Kirby\Toolkit\Xml;

class Feed
{
    public function xmlTag(string $xmlTag, string|null $fieldValue, bool $useCData = false, $attributes = []): string
    {
        if (!isset($fieldValue) && !is_null($fieldValue)) {
            return '';
        }

        if (empty($fieldValue) && !is_null($fieldValue)) {
            return '';
        }

        if ($useCData) {
            $value = '<![CDATA[' . $fieldValue . ']]>';
        } else {
            $value = Xml::encode($fieldValue);
        }

        $attr = [];
        if (count($attributes) > 0) {
            foreach ($attributes as $key => $attribute) {
                if (!is_null($attribute)) {
                    $attr[] = $key . '="' . $attribute . '"';
                }
            }
        }

        $attrString = count($attr) > 0 ? ' ' . implode(' ', $attr) : '';

        if (is_null($fieldValue)) {
            return '<' . $xmlTag . $attrString . '/>';
        }

        return '<' . $xmlTag . $attrString . '>' . $value . '</' . $xmlTag . '>';
    }

    public function getGuid($episode, $useUuid)
    {
        if ($useUuid) {
            return $episode->permalink();
        }

        $audio = $this->getAudioFile($episode);

        return $audio->guid()->value();
    }

    public function getAudioDuration($audio)
    {
        if (!isset($audio)) {
            return '0';
        }

        return $audio->duration()->value();
    }

    public function getAudioFile($episode)
    {
        if ($episode->podcasterAudio()->isNotEmpty()) {
            return $episode->podcasterAudio()->toFile();
        }

        return $episode->audio($episode->podcasterMp3()->first())->first()->toFile();
    }

    public function getAudioEnclosures($episode, $audio): array
    {
        return [
            'url' => $episode->url() . '/' . option(
                    'mauricerenck.podcaster.downloadTriggerPath',
                    'download'
                ) . '/' . $audio->filename(),
            'length' => $audio->size(),
        ];
    }

    public function getCoverFile($episode)
    {
        if ($episode->podcasterCover()->isNotEmpty()) {
            return $episode->podcasterCover()->toFile();
        }

        return null;
    }

    public function getChapters($episode)
    {
        if ($episode->podcasterChapters()->isEmpty()) {
            return [];
        }

        $chapterList = [];

        foreach ($episode->podcasterChapters()->toStructure() as $chapter) {
            $newChapter = [
                'start' => $chapter->podcasterChapterTimestamp()->value(),
                'title' => Xml::encode($chapter->podcasterChapterTitle()),
                'href' => null,
                'image' => null,
            ];

            if ($chapter->podcasterChapterUrl()->isNotEmpty()) {
                $newChapter['href'] = Xml::encode($chapter->podcasterChapterUrl());
            }

            if ($chapter->podcasterChapterImage()->isNotEmpty()) {
                $newChapter['image'] = Xml::encode($chapter->podcasterChapterImage()->toFile()->url());
            }

            $chapterList[] = $newChapter;
        }

        return $chapterList;
    }
}