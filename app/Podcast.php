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