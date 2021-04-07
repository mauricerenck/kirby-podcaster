<?php

namespace mauricerenck\Podcaster;

use mauricerenck\Podcaster\PodcasterUtils;
use mauricerenck\Podcaster\PodcasterAudioUtils;
use mauricerenck\Podcaster\PodcasterStats;
use mauricerenck\Podcaster\PodcasterStatsMatomo;
use mauricerenck\Podcaster\PodcasterStatsMySql;
use mauricerenck\Podcaster\PodcasterStatsSqlite;
use mauricerenck\Podcaster\PodcasterStatsPodTrac;
use mauricerenck\Podcaster\PodcasterWizard;
use Kirby;
use Kirby\Exception\Exception;
use Kirby\Http\Response;
use \PiwikTracker;

@include_once __DIR__ . '/vendor/autoload.php';

Kirby::plugin('mauricerenck/podcaster', [
    'options' => require_once(__DIR__ . '/config/options.php'),
    'templates' => [
        'podcasterfeed' => __DIR__ . '/templates/podcasterfeed.php'
    ],
    'blueprints' => [
        'pages/podcasterfeed' => __DIR__ . '/blueprints/pages/podcasterfeed.yml',
        'pages/podcasterwizard' => __DIR__ . '/blueprints/pages/podcasterwizard.yml',
        'tabs/podcasterepisode' => __DIR__ . '/blueprints/tabs/episode.yml',
        'files/podcaster-episode' => __DIR__ . '/blueprints/files/podcaster-episode.yml',
        'files/podcaster-cover' => __DIR__ . '/blueprints/files/podcaster-cover.yml'
    ],
    'tags' => [
        'podloveSubscribe' => require_once __DIR__ . '/tags/podloveSubscribe.php',
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
                'headline' => function ($headline = 'Monthly Downloads') {
                    return $headline;
                }
            ]
        ],
        'podcasterMonthlyGraph' => [
            'props' => [
                'headline' => function ($headline = 'Downloads this Month') {
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
                'headline' => function ($headline = 'Wizard') {
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
                $podcast = $podcasterUtils->getPageFromSlug($slug . '/' . option('mauricerenck.podcaster.defaultFeed', 'feed'));

                $stats = new PodcasterStats();
                $stats->increaseFeedVisits($podcast);

                return new Response($podcast->render(), 'text/xml');
            }
        ],
        [
            'pattern' => '(:all)/' . option('mauricerenck.podcaster.downloadTriggerPath', 'download') . '/(:any)',
            'action' => function ($slug, $filename) {
                $podcasterUtils = new PodcasterUtils();

                $episode = $podcasterUtils->getPageFromSlug($slug);
                $podcasterUtils->setCurrentEpisode($episode);
                $podcast = $podcasterUtils->getPodcastFeed($episode);

                $userAgent = $_SERVER['HTTP_USER_AGENT'];
                $userAgentData = $podcasterUtils->getUserAgent($userAgent);

                $stats = new PodcasterStats();
                $stats->increaseDownloads($podcast, $episode, $userAgentData);

                $filename = str_replace('.mp3', '', $filename);
                return $podcasterUtils->getPodcastFile();
            }
        ]
    ],
    'api' => require_once(__DIR__ . '/config/api.php'),
    'hooks' => require_once(__DIR__ . '/config/hooks.php')
]);
