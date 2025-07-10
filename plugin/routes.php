<?php

namespace mauricerenck\Podcaster;

use Kirby\Http\Response;

return [
    [
        'pattern' => '(:all)/' . option('mauricerenck.podcaster.defaultFeed', 'feed'),
        'action' => function ($slug) {

            $podcast = new Podcast();
            $feedParent = $podcast->getPageFromSlug($slug);
            $feed = $feedParent->children()->filterBy('intendedTemplate', 'podcasterfeed')->first();

            if (!isset($feed)) {
                $this->next();
            }

            $dbType = option('mauricerenck.podcaster.statsType', 'sqlite');

            switch ($dbType) {
                case 'sqlite':
                    $stats = new PodcasterStatsSqlite();
                    break;
                case 'mysql':
                    $stats = new PodcasterStatsMysql();
                    break;
                default:
                    $stats = new PodcasterStats();
                    break;
            }

            $stats->trackFeed($feed);

            return new Response($feed->render(), 'text/xml');
        },
    ],
    [
        'pattern' => '(:all)/' . option('mauricerenck.podcaster.downloadTriggerPath', 'download') . '/(:any)',
        'method' => 'GET|HEAD',
        'action' => function ($slug) {
            $podcast = new Podcast();

            $dbType = option('mauricerenck.podcaster.statsType', 'sqlite');
            switch ($dbType) {
                case 'sqlite':
                    $stats = new PodcasterStatsSqlite();
                    break;
                case 'mysql':
                    $stats = new PodcasterStatsMysql();
                    break;
                default:
                    $stats = new PodcasterStats();
                    break;
            }


            $userAgent = $_SERVER['HTTP_USER_AGENT'];

            $episode = $podcast->getPageFromSlug($slug);

            if (!isset($episode)) {
                $this->next();
            }

            $feed = $podcast->getFeedOfEpisode($episode);

            if (!isset($feed)) {
                $this->next();
            }

            $audio = $podcast->getAudioFile($episode);

            // Check if the request method is HEAD
            if (kirby()->request()->method() === 'HEAD') {
                // Send headers only, no body
                return $audio->header(false);
            }

            $stats->trackEpisode($feed, $episode, $userAgent);

            return $audio;
        },
    ],
    [
        'pattern' => 'podcaster/podlove/config/(:all)',
        'action' => function ($episodeSlug) {

            $podcast = new Podcast();
            $episode = $podcast->getPageFromSlug($episodeSlug);

            return new Response(json_encode($podcast->getPodloveConfigJson($episode)), 'application/json');
        }
    ],
    [
        'pattern' => 'podcaster/podlove/episode/(:all)',
        'action' => function ($episodeSlug) {
            $podcast = new Podcast();
            $episode = $podcast->getPageFromSlug($episodeSlug);

            return new Response(json_encode($podcast->getPodloveEpisodeJson($episode)), 'application/json');
        }
    ],
];
