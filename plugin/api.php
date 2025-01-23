<?php

namespace mauricerenck\Podcaster;

use Kirby\Http\Response;

return [
    'routes' => [
        [
            'pattern' => 'podcaster/stats/graph/downloads/(:any)/(:num)/(:num)',
            'action' => function ($podcastId, $year, $month) {
                if (option('mauricerenck.podcaster.statsInternal', false) === false) {
                    $errorMessage = ['error' => 'Internal stats are disabled, see documentation for more information'];

                    return new Response(json_encode($errorMessage), 'application/json', 412);
                }

                $dbType = option('mauricerenck.podcaster.statsType', 'sqlite');
                $stats = ($dbType === 'sqlite') ? new PodcasterStatsSqlite() : new PodcasterStatsMysql();

                $results = $stats->getDownloadsGraphData($podcastId, $year, $month);

                if ($results === false) {
                    return new Response(json_encode(['days' => []]), 'application/json');
                }

                $trackedDays = $results->toArray();
                $days = array_fill(0, 31, 0);

                foreach ($trackedDays as $day) {
                    $dayAsInt = (int)$day->day; // fix for sqlite queries returning day as string "01" instead of int 1
                    $days[$dayAsInt] = (int)$day->downloads;
                }

                return new Response(json_encode(['days' => $days]), 'application/json');
            },
        ],
        [
            'pattern' => 'podcaster/stats/graph/episodes/(:any)',
            'action' => function ($podcastId) {
                if (option('mauricerenck.podcaster.statsInternal', false) === false) {
                    $errorMessage = ['error' => 'Internal stats are disabled, see documentation for more information'];

                    return new Response(json_encode($errorMessage), 'application/json', 412);
                }

                $dbType = option('mauricerenck.podcaster.statsType', 'sqlite');
                $stats = ($dbType === 'sqlite') ? new PodcasterStatsSqlite() : new PodcasterStatsMysql();

                $results = $stats->getEpisodesGraphData($podcastId);

                if ($results === false) {
                    return new Response(json_encode([]), 'application/json');
                }

                $trackedMonths = $results->toArray();
                $downloadArray = [];
                $downloadJson = [];

                foreach ($trackedMonths as $entry) {
                    $year = (int)$entry->year;
                    $month = (int)$entry->month;
                    $downloads = (int)$entry->downloads;

                    if (!isset($downloadArray[$year])) {
                        $downloadArray[$year] = [];
                    }

                    $downloadArray[$year][$month] = $downloads;
                }

                foreach ($downloadArray as $year => $months) {
                    for ($i = 1; $i <= 12; $i++) {
                        $downloadJson[] = [
                            'downloads' => $months[$i] ?? 0,
                            'year' => $year,
                            'month' => $i,
                        ];
                    }
                }

                return new Response(json_encode(['downloads' => $downloadJson]), 'application/json');
            },
        ],
        [
            'pattern' => 'podcaster/stats/graph/feeds/(:any)',
            'action' => function ($podcastId) {
                if (option('mauricerenck.podcaster.statsInternal', false) === false) {
                    $errorMessage = ['error' => 'Internal stats are disabled, see documentation for more information'];

                    return new Response(json_encode($errorMessage), 'application/json', 412);
                }

                $dbType = option('mauricerenck.podcaster.statsType', 'sqlite');
                $stats = ($dbType === 'sqlite') ? new PodcasterStatsSqlite() : new PodcasterStatsMysql();

                $results = $stats->getFeedsGraphData($podcastId);

                if ($results === false) {
                    return new Response(json_encode([]), 'application/json');
                }

                $trackedMonths = $results->toArray();
                $downloadArray = [];
                $downloadJson = [];

                foreach ($trackedMonths as $entry) {
                    $year = (int)$entry->year;
                    $month = (int)$entry->month;
                    $downloads = (int)$entry->downloads;

                    if (!isset($downloadArray[$year])) {
                        $downloadArray[$year] = [];
                    }

                    $downloadArray[$year][$month] = $downloads;
                }

                foreach ($downloadArray as $year => $months) {
                    for ($i = 1; $i <= 12; $i++) {
                        $downloadJson[] = [
                            'downloads' => $months[$i] ?? 0,
                            'year' => $year,
                            'month' => $i,
                        ];
                    }
                }

                return new Response(json_encode(['downloads' => $downloadJson]), 'application/json');
            },
        ],
        [
            'pattern' => 'podcaster/stats/quickreports/(:any)',
            'action' => function ($podcastId) {
                if (option('mauricerenck.podcaster.statsInternal', false) === false) {
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
                    return new Response(json_encode(['reports' => []]), 'application/json');
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

                return new Response(json_encode(['reports' => $reports]), 'application/json');
            },
        ],
        [
            'pattern' => 'podcaster/stats/graph/episode/(:any)/(:any)',
            'action' => function ($podcastId, $episode) {
                if (option('mauricerenck.podcaster.statsInternal', false) === false) {
                    $errorMessage = ['error' => 'Internal stats are disabled, see documentation for more information'];

                    return new Response(json_encode($errorMessage), 'application/json', 412);
                }

                $dbType = option('mauricerenck.podcaster.statsType', 'sqlite');
                $stats = ($dbType === 'sqlite') ? new PodcasterStatsSqlite() : new PodcasterStatsMysql();

                $results = $stats->getEpisodeGraphData($podcastId, $episode);

                if ($results === false) {
                    return new Response(json_encode([]), 'application/json');
                }

                $cleandUpResult = [];
                foreach ($results->toArray() as $result) {
                    $cleandUpResult[] = [
                        'date' => $result->date,
                        'downloads' => (int)$result->downloads
                    ];
                }

                return new Response(json_encode($cleandUpResult), 'application/json');
            },
        ],
        [
            'pattern' => 'podcaster/stats/graph/devices/(:any)/(:num)/(:num)',
            'action' => function ($podcastId, $year, $month) {
                if (option('mauricerenck.podcaster.statsInternal', false) === false) {
                    $errorMessage = ['error' => 'Internal stats are disabled, see documentation for more information'];

                    return new Response(json_encode($errorMessage), 'application/json', 412);
                }

                $dbType = option('mauricerenck.podcaster.statsType', 'sqlite');
                $stats = ($dbType === 'sqlite') ? new PodcasterStatsSqlite() : new PodcasterStatsMysql();

                $results = $stats->getDevicesGraphData($podcastId, $year, $month);

                if ($results === false) {
                    return new Response(json_encode([]), 'application/json');
                }

                $total = 0;
                foreach ($results->toArray() as $result) {
                    $total += (int)$result->downloads;
                }

                return new Response(json_encode([
                    'total' => $total,
                    'data' => $results->toArray(),
                ]), 'application/json');
            },
        ],
        [
            'pattern' => 'podcaster/stats/graph/useragents/(:any)/(:num)/(:num)',
            'action' => function ($podcastId, $year, $month) {
                if (option('mauricerenck.podcaster.statsInternal', false) === false) {
                    $errorMessage = ['error' => 'Internal stats are disabled, see documentation for more information'];

                    return new Response(json_encode($errorMessage), 'application/json', 412);
                }

                $dbType = option('mauricerenck.podcaster.statsType', 'sqlite');
                $stats = ($dbType === 'sqlite') ? new PodcasterStatsSqlite() : new PodcasterStatsMysql();

                $results = $stats->getUserAgentGraphData($podcastId, $year, $month);

                if ($results === false) {
                    return new Response(json_encode([]), 'application/json');
                }

                $total = 0;
                foreach ($results->toArray() as $result) {
                    $total += (int)$result->downloads;
                }

                return new Response(json_encode([
                    'total' => $total,
                    'data' => $results->toArray(),
                ]), 'application/json');
            },
        ],
        [
            'pattern' => 'podcaster/stats/graph/os/(:any)/(:num)/(:num)',
            'action' => function ($podcastId, $year, $month) {
                if (option('mauricerenck.podcaster.statsInternal', false) === false) {
                    $errorMessage = ['error' => 'Internal stats are disabled, see documentation for more information'];

                    return new Response(json_encode($errorMessage), 'application/json', 412);
                }

                $dbType = option('mauricerenck.podcaster.statsType', 'sqlite');
                $stats = ($dbType === 'sqlite') ? new PodcasterStatsSqlite() : new PodcasterStatsMysql();

                $results = $stats->getSystemGraphData($podcastId, $year, $month);

                if ($results === false) {
                    return new Response(json_encode([]), 'application/json');
                }

                $total = 0;
                foreach ($results->toArray() as $result) {
                    $total += (int)$result->downloads;
                }

                return new Response(json_encode([
                    'total' => $total,
                    'data' => $results->toArray(),
                ]), 'application/json');
            },
        ],
        [
            'pattern' => 'podcaster/stats/top-episodes/(:any)',
            'action' => function ($podcastId) {
                if (option('mauricerenck.podcaster.statsInternal', false) === false) {
                    $errorMessage = ['error' => 'Internal stats are disabled, see documentation for more information'];

                    return new Response(json_encode($errorMessage), 'application/json', 412);
                }

                $dbType = option('mauricerenck.podcaster.statsType', 'sqlite');
                $stats = ($dbType === 'sqlite') ? new PodcasterStatsSqlite() : new PodcasterStatsMysql();

                $results = $stats->getTopEpisodes($podcastId);

                if ($results === false) {
                    return new Response(json_encode([]), 'application/json');
                }

                return new Response(json_encode($results->toArray()), 'application/json');
            },
        ],
        [
            'pattern' => 'podcaster/stats/episodes/(:any)',
            'action' => function ($podcastId) {
                if (option('mauricerenck.podcaster.statsInternal', false) === false) {
                    $errorMessage = ['error' => 'Internal stats are disabled, see documentation for more information'];

                    return new Response(json_encode($errorMessage), 'application/json', 412);
                }

                $podcastTools = new Podcast();
                $rssFeed = $podcastTools->getPodcastFromId($podcastId);

                if (!isset($rssFeed)) {
                    return new Response(json_encode([]), 'application/json');
                }

                $episodes = $podcastTools->getEpisodes($rssFeed);

                if ($episodes === false) {
                    return new Response(json_encode([]), 'application/json');
                }

                $episodeList = [];
                foreach ($episodes as $episode) {
                    $episodeList[] = [
                        'title' => $episode->title()->value(),
                        'slug' => $episode->uid(),
                    ];
                }

                return new Response(json_encode($episodeList), 'application/json');
            },
        ],
        [
            'pattern' => 'podcaster/stats/subscribers/(:any)',
            'action' => function ($podcastId) {
                if (option('mauricerenck.podcaster.statsInternal', false) === false) {
                    $errorMessage = ['error' => 'Internal stats are disabled, see documentation for more information'];

                    return new Response(json_encode($errorMessage), 'application/json', 412);
                }

                $podcastTools = new Podcast();
                $rssFeed = $podcastTools->getPodcastFromId($podcastId);

                if (!isset($rssFeed)) {
                    return new Response(json_encode(['estSubscribers' => 0]), 'application/json');
                }

                $episodes = $podcastTools->getEpisodes($rssFeed);

                if ($episodes === false || !isset($episodes)) {
                    return new Response(json_encode(['estSubscribers' => 0]), 'application/json');
                }

                $latestEpisodes = $episodes
                    ->filter(function ($child) {
                        return (int)$child->date()->toDate('U') <= time() - 48 * 60 * 60;
                    });

                if (!isset($latestEpisodes)) {
                    return new Response(json_encode(['estSubscribers' => 0]), 'application/json');
                }

                $latestEpisodes = $latestEpisodes->limit(3);

                $episodeList = [];
                foreach ($latestEpisodes as $episode) {
                    $episodeList[$episode->uid()] = date('Y-m-d', $episode->date()->toDate('U') + 24 * 60 * 60);
                }

                if (count($episodeList) === 0) {
                    return new Response(json_encode(['estSubscribers' => 0]), 'application/json');
                }

                $dbType = option('mauricerenck.podcaster.statsType', 'sqlite');
                $stats = ($dbType === 'sqlite') ? new PodcasterStatsSqlite() : new PodcasterStatsMysql();

                $results = $stats->getEstimatedSubscribers($podcastId, $episodeList);

                if ($results === false || count($results) === 0) {
                    return new Response(json_encode(['estSubscribers' => 0]), 'application/json');
                }

                $estSubscribers = 0;
                foreach ($results as $result) {
                    $estSubscribers += (int)$result->total_downloads;
                }

                $subs = $estSubscribers / count($results);

                return new Response(json_encode(['estSubscribers' => $subs]), 'application/json');
            },
        ],
    ],
];
