<?php

namespace mauricerenck\Podcaster;

use Kirby\Http\Response;

return [
    'routes' => [
        [
            'pattern' => 'podcaster/stats/graph/downloads/(:any)/(:num)/(:num)',
            'action' => function ($podcastId, $year, $month) {
                if (option('mauricerenck.podcaster.statsInternal') === false) {
                    $errorMessage = ['error' => 'Internal stats are disabled, see documentation for more information'];

                    return new Response(json_encode($errorMessage), 'application/json', 412);
                }

                $dbType = option('mauricerenck.podcaster.statsType', 'sqlite');
                $stats = ($dbType === 'sqlite') ? new PodcasterStatsSqlite() : new PodcasterStatsMysql();

                $results = $stats->getDownloadsGraphData($podcastId, $year, $month);

                if ($results === false) {
                    return ['days' => []];
                }

                $trackedDays = $results->toArray();
                $days = array_fill(0, 31, 0);

                foreach ($trackedDays as $day) {
                    $days[$day->day] = (integer)$day->downloads;
                }

                return ['days' => $days];
            },
        ],
        [
            'pattern' => 'podcaster/stats/quickreports/(:any)',
            'action' => function ($podcastId) {
                if (option('mauricerenck.podcaster.statsInternal') === false) {
                    $errorMessage = ['error' => 'Internal stats are disabled, see documentation for more information'];

                    return new Response(json_encode($errorMessage), 'application/json', 412);
                }

                $dbType = option('mauricerenck.podcaster.statsType', 'sqlite');
                $stats = ($dbType === 'sqlite') ? new PodcasterStatsSqlite() : new PodcasterStatsMysql();

                $year = date('Y');
                $month = date('n');
                $day = date('d');
                $weekStart = date('d', strtotime('monday this week'));
                $weekEnd = date('d', strtotime('sunday this week'));

                $results = $stats->getQuickReports($podcastId, $year, $month);

                if ($results === false) {
                    return ['reports' => []];
                }

                $trackedDays = $results->toArray();
                $today = 0;
                $thisWeek = 0;
                $thisMonth = 0;

                foreach ($trackedDays as $trackedDay) {
                    $downloads = (integer)$trackedDay->downloads;

                    if ($trackedDay === $day) {
                        $today = $downloads;
                    }

                    if ($trackedDay >= $weekStart && $trackedDay <= $weekEnd) {
                        $thisWeek += $downloads;
                    }

                    $thisMonth += $downloads;
                }

                $reports = [
                    'day' => $today,
                    'week' => $thisWeek,
                    'month' => $thisMonth,
                ];

                return ['reports' => $reports];
            },
        ],
        [
            'pattern' => 'podcaster/stats/graph/episode/(:any)/(:any)',
            'action' => function ($podcastId, $episode) {
                if (option('mauricerenck.podcaster.statsInternal') === false) {
                    $errorMessage = ['error' => 'Internal stats are disabled, see documentation for more information'];

                    return new Response(json_encode($errorMessage), 'application/json', 412);
                }

                $dbType = option('mauricerenck.podcaster.statsType', 'sqlite');
                $stats = ($dbType === 'sqlite') ? new PodcasterStatsSqlite() : new PodcasterStatsMysql();

                $results = $stats->getEpisodeGraphData($podcastId, $episode);

                if ($results === false) {
                    return [];
                }

                return $results->toArray();
            },
        ],
        [
            'pattern' => 'podcaster/stats/top-episodes/(:any)',
            'action' => function ($podcastId) {
                if (option('mauricerenck.podcaster.statsInternal') === false) {
                    $errorMessage = ['error' => 'Internal stats are disabled, see documentation for more information'];

                    return new Response(json_encode($errorMessage), 'application/json', 412);
                }

                $dbType = option('mauricerenck.podcaster.statsType', 'sqlite');
                $stats = ($dbType === 'sqlite') ? new PodcasterStatsSqlite() : new PodcasterStatsMysql();

                $results = $stats->getTopEpisodes($podcastId);

                if ($results === false) {
                    return [];
                }

                return $results->toArray();
            },
        ],
        [
            'pattern' => 'podcaster/stats/episodes/(:any)',
            'action' => function ($podcastId) {
                if (option('mauricerenck.podcaster.statsInternal') === false) {
                    $errorMessage = ['error' => 'Internal stats are disabled, see documentation for more information'];

                    return new Response(json_encode($errorMessage), 'application/json', 412);
                }

                $podcastTools = new Podcast();
                $rssFeed = $podcastTools->getPodcastFromId($podcastId);

                if (!isset($rssFeed)) {
                    return [];
                }

                $episodes = $podcastTools->getEpisodes($rssFeed);

                if ($episodes === false) {
                    return [];
                }

                $episodeList = [];
                foreach ($episodes as $episode) {
                    $episodeList[] = [
                        'title' => $episode->title()->value(),
                        'slug' => $episode->uid(),
                    ];
                }

                return $episodeList;
            },
        ],
    ],
];
