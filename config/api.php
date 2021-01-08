<?php

namespace Plugin\Podcaster;

use Xml;
use File;

return [
    'routes' => [
        [
            'pattern' => 'podcaster/stats/(:any)/year/(:num)/month/(:num)',
            'action' => function ($podcastId, $year, $month) {
                if (option('mauricerenck.podcaster.statsInternal') === false || option('mauricerenck.podcaster.statsType') === 'file') {
                    $errorMessage = ['error' => 'cannot use stats on file method, use mysql version instead'];
                    echo new Response(json_encode($errorMessage), 'application/json', 501);
                }

                $podcasterStats = new PodcasterStats();
                $stats = $podcasterStats->getEpisodeStatsByMonth($podcastId, $year, $month);
                return [
                    'stats' => $stats
                ];
            }
        ],
        [
            'pattern' => 'podcaster/stats/(:any)/(:any)/yearly-downloads/(:any)',
            'action' => function ($podcastId, $type, $year) {
                if (option('mauricerenck.podcaster.statsInternal') === false || option('mauricerenck.podcaster.statsType') === 'file') {
                    $errorMessage = ['error' => 'cannot use stats on file method, use mysql version instead'];
                    echo new Response(json_encode($errorMessage), 'application/json', 501);
                }

                $podcasterStats = new PodcasterStats();
                $stats = $podcasterStats->getDownloadsOfYear($podcastId, $year, $type);
                return [
                    'stats' => $stats
                ];
            }
        ],
        [
            'pattern' => 'podcaster/stats/(:any)/top/(:num)',
            'action' => function ($podcastId, $limit) {
                if (option('mauricerenck.podcaster.statsInternal') === false || option('mauricerenck.podcaster.statsType') === 'file') {
                    $errorMessage = ['error' => 'cannot use stats on file method, use mysql version instead'];
                    echo new Response(json_encode($errorMessage), 'application/json', 501);
                }

                $podcasterStats = new PodcasterStats();
                $stats = $podcasterStats->getTopDownloads($podcastId, $limit);

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
            'pattern' => 'podcaster/wizard/createFeed',
            'action' => function () {
                $headerTarget = $_SERVER['HTTP_X_TARGET_PAGE'];

                $targetPage = kirby()->page($headerTarget);
                $pageData = json_decode(file_get_contents('php://input'));

                $wizardHelper = new PodcasterWizard();

                $newPageData = [
                    'slug' => 'feed',
                    'template' => 'podcasterfeed',
                    'draft' => false,
                    'content' => [
                        'podcasterSource' => $targetPage->slug(),
                        'title' => $wizardHelper->getField($pageData, 'title'),
                        'podcasterTitle' => $wizardHelper->getField($pageData, 'title'),
                        'podcasterDescription' => $wizardHelper->getField($pageData, 'description'),
                        'podcasterSubtitle' => $wizardHelper->getField($pageData, 'itunessubtitle'),
                        'podcasterKeywords' => $wizardHelper->getField($pageData, 'ituneskeywords'),
                        'podcasterCopyright' => $wizardHelper->getField($pageData, 'copyright'),
                        'podcasterLink' => $wizardHelper->getField($pageData, 'link'),
                        'podcasterLanguage' => $wizardHelper->getField($pageData, 'language'),
                        'podcasterType' => $wizardHelper->getField($pageData, 'itunestype'),
                        'podcasterExplicit' => $wizardHelper->getField($pageData, 'itunesexplicit'),
                        'podcasterBlock' => $wizardHelper->getField($pageData, 'itunesblock')
                    ]
                ];

                $feed = $targetPage->createChild($newPageData);

                return json_encode(['title' => $pageData->title, 'slug' => $feed->id()]);
            },
            'method' => 'POST'
        ],
        [
            'pattern' => 'podcaster/wizard/createEpisode',
            'action' => function () {
                $headerTarget = $_SERVER['HTTP_X_TARGET_PAGE'];
                $headerTemplate = $_SERVER['HTTP_X_PAGE_TEMPLATE'];
                $pageStatus = ($_SERVER['HTTP_X_PAGE_STATUS'] === 'false');

                $targetPage = kirby()->page($headerTarget);
                $pageData = json_decode(file_get_contents('php://input'));

                $wizardHelper = new PodcasterWizard();
                $slug = $wizardHelper->getPageSlug($wizardHelper->getField($pageData, 'link'), $wizardHelper->getField($pageData, 'title'));

                $newPageData = [
                    'slug' => $slug,
                    'template' => $headerTemplate,
                    'draft' => $pageStatus,
                    'content' => [
                        'title' => $wizardHelper->getField($pageData, 'title'),
                        'date' => $wizardHelper->getField($pageData, 'pubDate'),
                        'podcasterSeason' => $wizardHelper->getField($pageData, 'itunesseason'),
                        'podcasterEpisode' => $wizardHelper->getField($pageData, 'itunesepisode'),
                        'podcasterEpisodeType' => $wizardHelper->getField($pageData, 'itunesepisodetype'),
                        'podcasterExplizit' => $wizardHelper->getField($pageData, 'itunesexplicit'),
                        'podcasterBlock' => $wizardHelper->getField($pageData, 'itunesblock'),
                        'podcasterTitle' => $wizardHelper->getField($pageData, 'title'),
                        'podcasterSubtitle' => $wizardHelper->getField($pageData, 'itunessubtitle'),
                        'podcasterDescription' => $wizardHelper->getField($pageData, 'description'),
                    ]
                ];

                $episode = $targetPage->createChild($newPageData);
                $mp3FileName = $slug . '.mp3';

                return json_encode(['title' => $pageData->title, 'slug' => $episode->id(), 'file' => $wizardHelper->getField($pageData, 'file')]);
            },
            'method' => 'POST'
        ],
        [
            'pattern' => 'podcaster/wizard/createFile',
            'action' => function () {
                $headerTarget = $_SERVER['HTTP_X_TARGET_PAGE'];

                $episode = kirby()->page($headerTarget);
                $pageData = json_decode(file_get_contents('php://input'));

                $wizardHelper = new PodcasterWizard();
                $slug = $episode->slug();

                $mp3FileName = $slug . '.mp3';
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

                return json_encode(['created' => $mp3FileName]);
            },
            'method' => 'POST'
        ]
    ]
];
