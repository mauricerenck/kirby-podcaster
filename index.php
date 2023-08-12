<?php

namespace mauricerenck\Podcaster;

use Kirby;
use Kirby\Http\Response;
use Kirby\Http\Remote;

@include_once __DIR__ . '/vendor/autoload.php';

Kirby::plugin('mauricerenck/podcaster', [
    'options' => require_once(__DIR__ . '/internal/options.php'),
    'blueprints' => [
        'pages/podcasterfeed' => __DIR__ . '/blueprints/pages/feed.yml',

        'tabs/podcasterepisode' => __DIR__ . '/blueprints/tabs/episode.yml', // backwards compatibility
        'tabs/podcaster/episode' => __DIR__ . '/blueprints/tabs/episode.yml',
        'tabs/podcaster/feed-base' => __DIR__ . '/blueprints/tabs/feed-base.yml',
        'tabs/podcaster/feed-details' => __DIR__ . '/blueprints/tabs/feed-details.yml',
        'tabs/podcaster/feed-stats' => __DIR__ . '/blueprints/tabs/feed-stats.yml',
        'tabs/podcaster/feed-player' => __DIR__ . '/blueprints/tabs/feed-player.yml',
        'tabs/podcaster/feed-tracking' => __DIR__ . '/blueprints/tabs/feed-tracking.yml',

        //'pages/podcasterwizard' => __DIR__ . '/blueprints/pages/podcasterwizard.yml',
        'files/podcaster-episode' => __DIR__ . '/blueprints/files/podcaster-episode.yml',
        'files/podcaster-cover' => __DIR__ . '/blueprints/files/podcaster-cover.yml',
    ],
    'templates' => [
        'podcasterfeed' => __DIR__ . '/templates/podcasterfeed.php',
    ],
    'pageMethods' => require_once __DIR__ . '/app/FeedMethods.php',
    'api' => require_once(__DIR__ . '/internal/api.php'),
    'hooks' => require_once(__DIR__ . '/internal/hooks.php'),
    'areas' => require_once(__DIR__ . '/internal/areas.php'),
    'snippets' => [
        'podcaster-feed-header' => __DIR__ . '/snippets/xml/xml-header.php',
        'podcaster-feed-cover' => __DIR__ . '/snippets/xml/channel-cover.php',
        'podcaster-feed-author' => __DIR__ . '/snippets/xml/channel-author.php',
        'podcaster-feed-owner' => __DIR__ . '/snippets/xml/channel-owner.php',
        'podcaster-feed-description' => __DIR__ . '/snippets/xml/channel-description.php',
        'podcaster-feed-categories' => __DIR__ . '/snippets/xml/channel-categories.php',
        'podcaster-feed-item' => __DIR__ . '/snippets/xml/item.php',
        'podcaster-feed-item-cover' => __DIR__ . '/snippets/xml/item-cover.php',
        'podcaster-feed-item-chapter' => __DIR__ . '/snippets/xml/item-chapter.php',
        'podcaster-feed-item-enclosure' => __DIR__ . '/snippets/xml/item-enclosure.php',

        'podcaster-player' => __DIR__ . '/snippets/frontend/player.php',
        'podcaster-podlove-player' => __DIR__ . '/snippets/frontend/podlove-player.php',
        'podcaster-html5-player' => __DIR__ . '/snippets/frontend/html5-player.php',

        'podcaster-ogaudio' => __DIR__ . '/snippets/frontend/og-audio.php',
    ],
    'routes' => [
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

                $stats->trackEpisode($feed, $episode, $userAgent);
                // $stats->calculateCarbonEmissions($feed, $episode);

                return $podcast->getAudioFile($episode);
            },
        ],
        [
            'pattern' => 'podcaster/podlove/roles/(:all)',
            'action' => function ($episodeSlug) {
                $podcast = new Podcast();

                return json_encode($podcast->getPodloveRoles($episodeSlug));
            }
        ],
        [
            'pattern' => 'podcaster/podlove/groups/(:all)',
            'action' => function ($episodeSlug) {
                $podcast = new Podcast();

                return json_encode($podcast->getPodloveRoles($episodeSlug));
            }
        ],
        [
            'pattern' => 'podcaster/podlove/config/(:all)',
            'action' => function ($episodeSlug) {

                $podcast = new Podcast();
                $episode = $podcast->getPageFromSlug($episodeSlug);

                return json_encode($podcast->getPodloveConfigJson($episode));
            }
        ],
        [
            'pattern' => 'podcaster/podlove/episode/(:all)',
            'action' => function ($episodeSlug) {
                $podcast = new Podcast();
                $episode = $podcast->getPageFromSlug($episodeSlug);

                return json_encode($podcast->getPodloveEpisodeJson($episode));
            }
        ],
        [
            'pattern' => 'podcaster/api/(categories|languages|podlove-clients)',
            'action' => function ($endpoint) {
                if (option('mauricerenck.podcaster.useApi', true)) {
                    $apiCache = kirby()->cache('mauricerenck.podcaster');
                    $$endpoint  = $apiCache->get($endpoint);

                    if ($$endpoint === null) {
                        $response = new Remote('https://api.podcaster-plugin.com/' . $endpoint);
                        $apiCache->set($endpoint, $response->content(), 7* 24 * 60 );
                    }

                    return $$endpoint;
                }

                $json = file_get_contents(__DIR__ . '/res/' . $endpoint . '.json');
                return $json;
            }
        ],
    ],
]);
