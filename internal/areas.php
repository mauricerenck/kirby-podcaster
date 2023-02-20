<?php

namespace mauricerenck\Podcaster;

return [
    'podcasts' => function ($kirby) {
        return [
            'label' => 'Podcasts',
            'icon' => 'audio',
            'menu' => true,
            'link' => 'podcasts',
            'views' => [
                [
                    'pattern' => ['podcasts'],
                    'action' => function () {
                        return [
                            'component' => 'k-podcaster-view',
                            'title' => 'Podcasts',
                            'props' => [
                                'month' => date('n'),
                                'year' => date('Y'),
                                'podcasts' => function () {
                                    $podcastTools = new Podcast();
                                    return $podcastTools->getAllPodcasts();
                                },
                            ],
                        ];
                    },
                ],
            ],
        ];
    },
];
