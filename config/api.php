<?php

namespace Plugin\Podcaster;

use Xml;
use File;

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

                $wizardHelper = new PodcasterWizard();
                $slug = $wizardHelper->getPageSlug($wizardHelper->getField($pageData, 'link'), $wizardHelper->getField($pageData, 'title'));

                $newPageData = [
                    'slug' => $slug,
                    'template' => $headerTemplate,
                    'content' => [
                        'title' => $wizardHelper->getField($pageData, 'title'),
                        'date' => $wizardHelper->getField($pageData, 'pubDate'),
                        'podcasterSeason' => $wizardHelper->getField($pageData, 'itunesseason'),
                        'podcasterEpisode' => $wizardHelper->getField($pageData, 'title'), // TODO
                        'podcasterEpisodeType' => $wizardHelper->getField($pageData, 'title'), // TODO
                        'podcasterExplizit' => $wizardHelper->getField($pageData, 'itunesexplicit'),
                        'podcasterBlock' => $wizardHelper->getField($pageData, 'itunesblock'),
                        'podcasterTitle' => $wizardHelper->getField($pageData, 'title'), // TODO
                        'podcasterSubtitle' => $wizardHelper->getField($pageData, 'itunessubtitle'),
                        'podcasterDescription' => $wizardHelper->getField($pageData, 'description'),
                    ]
                ];

                $episode = $targetPage->createChild($newPageData);
                $mp3FileName = $wizardHelper->getPageSlug($wizardHelper->getField($pageData, 'file'), $slug . '.mp3');
                $mp3 = $wizardHelper->downloadMp3($wizardHelper->getField($pageData, 'file'), $mp3FileName);

                $file = File::create([
                    'source' => kirby()->root('plugins') . '/kirby-podcaster/tmp/' . $mp3FileName,
                    'parent' => $episode,
                    'filename' => $mp3FileName,
                    'template' => 'podcaster-episode',
                    'content' => [
                        'duration' => $wizardHelper->getField($pageData, 'itunesduration'),
                        'episodeTitle' => $wizardHelper->getField($pageData, 'itunestitle')
                    ]
                ]);

                unlink(kirby()->root('plugins') . '/kirby-podcaster/tmp/' . $mp3FileName);

                return $pageData->title;
            },
            'method' => 'POST'
        ]
    ]
];
