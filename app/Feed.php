<?php

namespace mauricerenck\Podcaster;

use Kirby\Toolkit\Xml;
use Kirby\Toolkit\Str;
use IntlDateFormatter;


class Feed extends Podcast
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

        if (is_null($audio)) {
            return '';
        }

        return $audio->guid()->value();
    }

    public function getAudioDuration($audio)
    {
        if (!isset($audio)) {
            return '0';
        }

        return $audio->duration()->value();
    }

    public function getAudioEnclosures($episode, $audio): array
    {
        return [
            'url' =>
                $episode->url() .
                '/' .
                option('mauricerenck.podcaster.downloadTriggerPath', 'download') .
                '/' .
                $audio->filename(),
            'length' => $audio->size(),
            'type' => $audio->mime(),
        ];
    }

    public function getCoverFile($episode)
    {
        if ($episode->podcasterCover()->isNotEmpty()) {
            return $episode->podcasterCover()->toFile();
        }

        return null;
    }

    public function getChapters($episode, $returnEmptyFields = false, $milliseconds = false)
    {
        if ($episode->podcasterChapters()->isEmpty()) {
            return [];
        }

        $chapterList = [];

        foreach ($episode->podcasterChapters()->toStructure() as $chapter) {
            $chapterStart = $milliseconds
                ? $chapter->podcasterChapterTimestamp()->value() . '.000'
                : $chapter->podcasterChapterTimestamp()->value();

            $newChapter = [
                'start' => $chapterStart,
                'title' => Str::esc($chapter->podcasterChapterTitle()),
                'href' => $returnEmptyFields ? '' : null,
                'image' => $returnEmptyFields ? '' : null,
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

    public function getTranscript($episode)
    {
        if ($episode->podcasterTranscript()->isEmpty()) {
            return [];
        }

        $transcriptList = [];

        foreach ($episode->podcasterTranscript()->toStructure() as $transcript) {
            $file = $transcript->podcasterTranscriptFile()->toFile();
            $mimeType = $file->extension() === 'vtt' ? 'text/vtt' : 'application/srt';

            $newTranscript = [
                'url' => $file->permalink(),
                'type' => $mimeType,
                'language' => $transcript->podcasterTranscriptLanguage()->value(),
            ];

            $transcriptList[] = $newTranscript;
        }

        return $transcriptList;
    }

    public function getRssDate($timestamp)
    {
        $formatter = new IntlDateFormatter('en_US', IntlDateFormatter::FULL, IntlDateFormatter::FULL, 'UTC', IntlDateFormatter::GREGORIAN, "EEE, dd MMM yyyy HH:mm:ss +0000");
        return datefmt_format($formatter,$timestamp);
    }
}
