<?php

namespace Plugin\Podcaster;

use Kirby;
use Kirby\Exception\Exception;
use \PiwikTracker;
use Kirby\Http\Response;

@include_once __DIR__ . '/vendor/autoload.php';

load([
    'Plugin\Podcaster\PodcasterUtils' => 'utils/PodcasterUtils.php',
    'Plugin\Podcaster\PodcasterAudioUtils' => 'utils/PodcasterAudioUtils.php',
    'Plugin\Podcaster\PodcasterStats' => 'utils/PodcasterStats.php',
    'Plugin\Podcaster\PodcasterStatsMySql' => 'utils/PodcasterStatsMysql.php',
    'Plugin\Podcaster\PodcasterStatsFile' => 'utils/PodcasterStatsFile.php',
    'Plugin\Podcaster\PodcasterStatsPodTrac' => 'utils/PodcasterStatsPodTrac.php',
    'Plugin\Podcaster\PodcasterWizard' => 'utils/PodcasterWizard.php',
    'Plugin\Podcaster\PiwikTracker' => 'lib/PiwikTracker.php'
], __DIR__);

Kirby::plugin('mauricerenck/podcaster', [
    'options' => require_once(__DIR__ . '/config/options.php'),
    'templates' => [
        'podcasterfeed' => __DIR__ . '/templates/podcasterfeed.php'
    ],
    'blueprints' => [
        'pages/podcasterfeed' => __DIR__ . '/blueprints/pages/podcasterfeed.yml',
        'pages/podcasterwizard' => __DIR__ . '/blueprints/pages/podcasterwizard.yml',
        'tabs/podcasterepisode' => __DIR__ . '/blueprints/tabs/episode.yml',
        'files/podcaster-episode' => __DIR__ . '/blueprints/files/podcaster-episode.yml'
    ],
    'sections' => [
        'podcasterEpisodeStats' => [
            'props' => [
                'headline' => function ($headline = 'Last modified') {
                    return $headline;
                }
            ]
        ],
        'podcasterYearlyGraph' => [
            'props' => [
                'headline' => function ($headline = 'Last modified') {
                    return $headline;
                }
            ]
        ],
        'podcasterTopTen' => [
            'props' => [
                'headline' => function ($headline = 'Top 10 Episodes') {
                    return $headline;
                }
            ]
        ],
        'podcasterFeedStats' => [
            'props' => [
                'headline' => function ($headline = 'Feed Downloads') {
                    return $headline;
                }
            ]
        ],
        'podcasterWizard' => [
            'props' => [
                'headline' => function ($headline = 'Feed Downloads') {
                    return $headline;
                }
            ]
        ]
    ],
    'snippets' => [
        'podcaster-player' => __DIR__ . '/snippets/podcaster-player.php',
        'podcaster-podlove-player' => __DIR__ . '/snippets/podlove-player.php',
        'podcaster-html5-player' => __DIR__ . '/snippets/html5-player.php',
        'podcaster-ogaudio' => __DIR__ . '/snippets/og-audio.php'
    ],
    'routes' => [
        [
            'pattern' => '(:all)/podcaster-feed-style',
            'action' => function () {
                $string = file_get_contents(__DIR__ . '/res/feed-style.xsl');
                return new Response($string, 'text/xml');
            }
        ],
        [
            'pattern' => '(:all)/' . option('mauricerenck.podcaster.defaultFeed', 'feed'),
            'action' => function ($slug) {
                $podcasterUtils = new PodcasterUtils();

                $feed = $podcasterUtils->getPageFromSlug($slug . '/' . option('mauricerenck.podcaster.defaultFeed', 'feed'));

                if (option('mauricerenck.podcaster.statsInternal') === true) {
                    $stats = new PodcasterStats();
                    $trackingDate = time();
                    $stats->increaseFeedVisits($feed, $trackingDate);
                }

                if ($feed->podcasterMatomoFeedEnabled()->isTrue()) {
                    $matomo = new PiwikTracker($feed->podcasterMatomoFeedSiteId(), option('mauricerenck.podcaster.matomoBaseUrl'));

                    $matomo->setTokenAuth(option('mauricerenck.podcaster.matomoToken'));
                    $matomo->disableSendImageResponse();
                    $matomo->disableCookieSupport();
                    $matomo->setUrl($feed->url());
                    $matomo->setIp($_SERVER['REMOTE_ADDR']);

                    if ($feed->podcasterMatomoFeedPage()->isNotEmpty() && $feed->podcasterMatomoFeedPage()->isTrue()) {
                        $matomo->doTrackPageView($feed->podcasterTitle());
                    }

                    if ($feed->podcasterMatomoFeedGoalId()->isNotEmpty()) {
                        $matomo->doTrackGoal($feed->podcasterMatomoFeedGoalId(), 1);
                    }

                    if ($feed->podcasterMatomoFeedEventName()->isNotEmpty()) {
                        $matomo->doTrackEvent($feed->podcasterTitle(), $feed->podcasterMatomoFeedEventName(), 1);
                    }

                    if ($feed->podcasterMatomoFeedAction()->isTrue()) {
                        $matomo->doTrackAction($feed->url(), 'download');
                    }
                }

                return new Response($feed->render(), 'text/xml');
            }
        ],
        [
            'pattern' => '(:all)/' . option('mauricerenck.podcaster.downloadTriggerPath', 'download') . '/(:any)',
            'action' => function ($slug, $filename) {
                $podcasterUtils = new PodcasterUtils();
                $episode = $podcasterUtils->getPageFromSlug($slug);
                $podcasterUtils->setCurrentEpisode($episode);

                $podcast = $episode->siblings()->find(option('mauricerenck.podcaster.defaultFeed', 'feed'));

                if (option('mauricerenck.podcaster.statsInternal') === true) {
                    $stats = new PodcasterStats();
                    $trackingDate = time();
                    $stats->increaseDownloads($episode, $trackingDate);
                }

                if ($podcast->podcasterMatomoEnabled()->isTrue()) {
                    $matomo = new PiwikTracker($podcast->podcasterMatomoSiteId(), option('mauricerenck.podcaster.matomoBaseUrl'));

                    // setup
                    $matomo->setTokenAuth(option('mauricerenck.podcaster.matomoToken'));
                    $matomo->disableSendImageResponse();
                    $matomo->disableCookieSupport();
                    $matomo->setUrl($episode->url());
                    $matomo->setIp($_SERVER['REMOTE_ADDR']);

                    if ($podcast->podcasterMatomoGoalId()->isNotEmpty()) {
                        $matomo->doTrackGoal($podcast->podcasterMatomoGoalId(), 1);
                    }

                    if ($podcast->podcasterMatomoEventName()->isNotEmpty()) {
                        $matomo->doTrackEvent($podcast->podcasterTitle(), $episode->title(), $podcast->podcasterMatomoEventName());
                    }

                    if ($podcast->podcasterMatomoAction()->isTrue()) {
                        $matomo->doTrackAction($episode->url(), 'download');
                    }
                }

                $filename = str_replace('.mp3', '', $filename);
                return $podcasterUtils->getPodcastFile();
            }
        ]
    ],
    'api' => require_once(__DIR__ . '/config/api.php'),
    'hooks' => require_once(__DIR__ . '/config/hooks.php')
]);
