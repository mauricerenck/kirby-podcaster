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
                    $days[$day->day] = (int)$day->downloads;
                }

                return ['days' => $days];
            },
        ],
        [
            'pattern' => 'podcaster/stats/graph/episodes/(:any)',
            'action' => function ($podcastId) {
                if (option('mauricerenck.podcaster.statsInternal') === false) {
                    $errorMessage = ['error' => 'Internal stats are disabled, see documentation for more information'];

                    return new Response(json_encode($errorMessage), 'application/json', 412);
                }

                $dbType = option('mauricerenck.podcaster.statsType', 'sqlite');
                $stats = ($dbType === 'sqlite') ? new PodcasterStatsSqlite() : new PodcasterStatsMysql();

                $results = $stats->getEpisodesGraphData($podcastId);

                if ($results === false) {
                    return [];
                }

                $trackedMonths = $results->toArray();
                $downloads = [];

                foreach ($trackedMonths as $month) {
                    $downloads[] = [
                        'downloads' => (int)$month->downloads,
                        'year' => (int)$month->year,
                        'month' => (int)$month->month,
                    ];
                }

                return ['downloads' => $downloads];
            },
        ],
        [
            'pattern' => 'podcaster/stats/graph/feeds/(:any)',
            'action' => function ($podcastId) {
                if (option('mauricerenck.podcaster.statsInternal') === false) {
                    $errorMessage = ['error' => 'Internal stats are disabled, see documentation for more information'];

                    return new Response(json_encode($errorMessage), 'application/json', 412);
                }

                $dbType = option('mauricerenck.podcaster.statsType', 'sqlite');
                $stats = ($dbType === 'sqlite') ? new PodcasterStatsSqlite() : new PodcasterStatsMysql();

                $results = $stats->getFeedsGraphData($podcastId);

                if ($results === false) {
                    return [];
                }

                $trackedMonths = $results->toArray();
                $downloads = [];

                foreach ($trackedMonths as $month) {
                    $downloads[] = [
                        'downloads' => (int)$month->downloads,
                        'year' => (int)$month->year,
                        'month' => (int)$month->month,
                    ];
                }

                return ['downloads' => $downloads];
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

                $trackedDays = $results['detailed']->toArray();
                $today = 0;
                $thisWeek = 0;
                $thisMonth = 0;

                foreach ($trackedDays as $trackedDay) {
                    $downloads = (int)$trackedDay->downloads;

                    if ($trackedDay->day == $day) {
                        $today = $downloads;
                    }

                    if ($trackedDay->day >= $weekStart && $trackedDay->day <= $weekEnd) {
                        $thisWeek += $downloads;
                    }

                    $thisMonth += $downloads;
                }

                $overall = $results['overall']->toArray();
                $allTime = $overall[0]->downloads ?? 0;

                $reports = [
                    'day' => $today,
                    'week' => $thisWeek,
                    'month' => $thisMonth,
                    'overall' => $allTime,
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
            'pattern' => 'podcaster/stats/graph/devices/(:any)/(:num)/(:num)',
            'action' => function ($podcastId, $year, $month) {
                if (option('mauricerenck.podcaster.statsInternal') === false) {
                    $errorMessage = ['error' => 'Internal stats are disabled, see documentation for more information'];

                    return new Response(json_encode($errorMessage), 'application/json', 412);
                }

                $dbType = option('mauricerenck.podcaster.statsType', 'sqlite');
                $stats = ($dbType === 'sqlite') ? new PodcasterStatsSqlite() : new PodcasterStatsMysql();

                $results = $stats->getDevicesGraphData($podcastId, $year, $month);

                if ($results === false) {
                    return [];
                }

                $total = 0;
                foreach ($results->toArray() as $result) {
                    $total += $result->downloads;
                }

                return [
                    'total' => $total,
                    'data' => $results->toArray(),
                ];
            },
        ],
        [
            'pattern' => 'podcaster/stats/graph/useragents/(:any)/(:num)/(:num)',
            'action' => function ($podcastId, $year, $month) {
                if (option('mauricerenck.podcaster.statsInternal') === false) {
                    $errorMessage = ['error' => 'Internal stats are disabled, see documentation for more information'];

                    return new Response(json_encode($errorMessage), 'application/json', 412);
                }

                $dbType = option('mauricerenck.podcaster.statsType', 'sqlite');
                $stats = ($dbType === 'sqlite') ? new PodcasterStatsSqlite() : new PodcasterStatsMysql();

                $results = $stats->getUserAgentGraphData($podcastId, $year, $month);

                if ($results === false) {
                    return [];
                }

                $total = 0;
                foreach ($results->toArray() as $result) {
                    $total += $result->downloads;
                }

                return [
                    'total' => $total,
                    'data' => $results->toArray(),
                ];
            },
        ],
        [
            'pattern' => 'podcaster/stats/graph/os/(:any)/(:num)/(:num)',
            'action' => function ($podcastId, $year, $month) {
                if (option('mauricerenck.podcaster.statsInternal') === false) {
                    $errorMessage = ['error' => 'Internal stats are disabled, see documentation for more information'];

                    return new Response(json_encode($errorMessage), 'application/json', 412);
                }

                $dbType = option('mauricerenck.podcaster.statsType', 'sqlite');
                $stats = ($dbType === 'sqlite') ? new PodcasterStatsSqlite() : new PodcasterStatsMysql();

                $results = $stats->getSystemGraphData($podcastId, $year, $month);

                if ($results === false) {
                    return [];
                }

                $total = 0;
                foreach ($results->toArray() as $result) {
                    $total += $result->downloads;
                }

                return [
                    'total' => $total,
                    'data' => $results->toArray(),
                ];
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
        [
            'pattern' => 'podcaster/stats/subscribers/(:any)',
            'action' => function ($podcastId) {
                if (option('mauricerenck.podcaster.statsInternal') === false) {
                    $errorMessage = ['error' => 'Internal stats are disabled, see documentation for more information'];

                    return new Response(json_encode($errorMessage), 'application/json', 412);
                }

                $podcastTools = new Podcast();
                $rssFeed = $podcastTools->getPodcastFromId($podcastId);

                if (!isset($rssFeed)) {
                    return ['estSubscribers' => 0];
                }

                $episodes = $podcastTools->getEpisodes($rssFeed);

                if ($episodes === false || !isset($episodes)) {
                    return ['estSubscribers' => 0];
                }

                $latestEpisodes = $episodes
                    ->filter(function ($child) {
                        return (int)$child->date()->toDate('U') <= time() - 48 * 60 * 60;
                    });

                if (!isset($latestEpisodes)) {
                    return ['estSubscribers' => 0];
                }

                $latestEpisodes = $latestEpisodes->limit(3);

                $episodeList = [];
                foreach ($latestEpisodes as $episode) {
                    $episodeList[$episode->uid()] = date('Y-m-d', $episode->date()->toDate('U') + 420 * 60 * 60);
                    // FIXME
                    // $episodeList[$episode->uid()] = date('Y-m-d', $episode->date()->toDate('U') + 24 * 60 * 60);
                }

                if (count($episodeList) === 0) {
                    return ['estSubscribers' => 0];
                }

                $dbType = option('mauricerenck.podcaster.statsType', 'sqlite');
                $stats = ($dbType === 'sqlite') ? new PodcasterStatsSqlite() : new PodcasterStatsMysql();

                $results = $stats->getEstimatedSubscribers($podcastId, $episodeList);

                if ($results === false || count($results) === 0) {
                    return ['estSubscribers' => 0];
                }

                $estSubscribers = 0;
                foreach ($results as $result) {
                    $estSubscribers += $result->total_downloads;
                }

                $subs = $estSubscribers / count($results);

                return ['estSubscribers' => $subs];
            },
        ],
    ],
];
