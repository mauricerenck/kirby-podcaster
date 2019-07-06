<?php

namespace Plugin\Podcaster;

use Xml;

return [
    'routes' => [
        [
            'pattern' => 'podcaster/stats/(:any)/year/(:num)/month/(:num)',
            'action' => function ($podcast, $year, $month) {
                if (option('mauricerenck.podcaster.statsInternal') === false || option('mauricerenck.podcaster.statsType') === 'file') {
                    $errorMessage = ['error' => 'cannot use stats on file method, use mysql version instead'];
                    echo new Response(json_encode($errorMessage), 'application/json', 501);
                }

                $podcasterStats = new PodcasterStats();
                $stats = $podcasterStats->getEpisodeStatsOfMonth($podcast, $year, $month);
                return [
                    'stats' => $stats
                ];
            }
        ],
        [
            'pattern' => 'podcaster/stats/(:any)/(:any)/yearly-downloads/(:any)',
            'action' => function ($podcast, $type, $year) {
                if (option('mauricerenck.podcaster.statsInternal') === false || option('mauricerenck.podcaster.statsType') === 'file') {
                    $errorMessage = ['error' => 'cannot use stats on file method, use mysql version instead'];
                    echo new Response(json_encode($errorMessage), 'application/json', 501);
                }

                $podcasterStats = new PodcasterStats();
                $stats = $podcasterStats->getDownloadsOfYear($podcast, $year, $type);
                return [
                    'stats' => $stats
                ];
            }
        ],
        [
            'pattern' => 'podcaster/stats/(:any)/top/(:num)',
            'action' => function ($podcast, $limit) {
                if (option('mauricerenck.podcaster.statsInternal') === false || option('mauricerenck.podcaster.statsType') === 'file') {
                    $errorMessage = ['error' => 'cannot use stats on file method, use mysql version instead'];
                    echo new Response(json_encode($errorMessage), 'application/json', 501);
                }

                $podcasterStats = new PodcasterStats();
                $stats = $podcasterStats->getTopDownloads($podcast, $limit);
                return [
                    'stats' => $stats
                ];
            }
        ],
        [
            'pattern' => 'podcaster/wizard/checkfeed',
            'action' => function () {
                try {
                    $feedUrl = $_SERVER['HTTP_X_FEED_URI'];
                } catch (Exeption $e) {
                    echo 'Could not read feed';
                }

                $feed = implode(file($feedUrl));
                return Xml::parse($feed);
            }
        ],
        [
            'pattern' => 'podcaster/wizard/createEpisode',
            'action' => function () {
                $headerTarget = $_SERVER['HTTP_X_TARGET_PAGE'];
                $headerTemplate = $_SERVER['HTTP_X_PAGE_TEMPLATE'];

                $targetPage = kirby()->page($headerTarget);
                $pageData = json_decode(file_get_contents('php://input'));

                $slug = $end = array_slice(explode('/', rtrim($pageData->link, '/')), -1)[0];

                $newPageData = [
                    'slug' => $slug,
                    'template' => $headerTemplate,
                    'content' => [
                        'title' => $pageData->title,
                        'date' => $pageData->pubDate,
                        'podcasterSeason' => $pageData->itunesseason,
                        'podcasterEpisode' => $pageData->title, // TODO
                        'podcasterEpisodeType' => $pageData->title, // TODO
                        'podcasterExplizit' => $pageData->itunesexplicit,
                        'podcasterBlock' => $pageData->itunesblock,
                        'podcasterTitle' => $pageData->title, // TODO
                        'podcasterSubtitle' => $pageData->itunessubtitle,
                        'podcasterDescription' => $pageData->description,
                    ]
                ];

                $targetPage->createChild($newPageData);
                return $pageData->title;
            },
            'method' => 'POST'
        ]
    ]
];
