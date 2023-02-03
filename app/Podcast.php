<?php

namespace mauricerenck\Podcaster;

use Exception;
use Kirby\Filesystem\F;

class Podcast
{
    public function getPageFromSlug(string $slug)
    {
        if ($slug == '') {
            $page = page(site()->homePageId());
        } elseif (!$page = page($slug)) {
            $page = page(kirby()->router()->call($slug));

            if ($page->isHomeOrErrorPage()) {
                return false;
            }
        }

        if (is_null($page)) {
            return false;
        }

        return $page;
    }

    public function getFeedOfEpisode($episode)
    {
        $feed = $episode->siblings()->filterBy('intendedTemplate', 'podcasterfeed')->first();

        if (is_null($feed) || $feed->count() === 0) {
            $feed = $episode->parent()->parent()->children()->filterBy('intendedTemplate', 'podcasterfeed')->first();
        }

        return $feed;
    }

    public function getEpisodes($rssFeed)
    {
        return $rssFeed->podcasterSource()
            ->toPages()->children()
            ->listed()
            ->filter(function ($child) {
                return $child->date()->toDate() <= time();
            })
            ->filter(function ($child) {
                return $child->hasAudio();
            })->sortBy('date', 'desc');
    }

    public function getAudioFile($episode)
    {
        if ($episode->podcasterAudio()->isNotEmpty()) {
            return $episode->podcasterAudio()->toFile();
        }

        return $episode->audio($episode->podcasterMp3()->first())->first()->toFile();
    }

    public function getPluginVersion()
    {
        try {
            $composerString = F::read(__DIR__ . '/../composer.json');
            $composerJson = json_decode($composerString);

            return $composerJson->version;
        } catch (Exception $e) {
            return 'Unknown';
        }
    }
}