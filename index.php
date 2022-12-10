<?php

namespace mauricerenck\Podcaster;

use Kirby;

@include_once __DIR__ . '/vendor/autoload.php';

Kirby::plugin('mauricerenck/podcaster', [
    'options' => require_once(__DIR__ . '/internal/options.php'),
    'routes' => [
        [
            'pattern' => '(:all)/' . option('mauricerenck.podcaster.defaultFeed', 'feed'),
            'action' => function ($slug) {
                $podcasterUtils = new Podcast();
                $podcast = $podcasterUtils->getPageFromSlug(
                    $slug . '/' . option('mauricerenck.podcaster.defaultFeed', 'feed')
                );

                //$stats = new PodcasterStats();
                //$stats->increaseFeedVisits($podcast);

                return new Response($podcast->render(), 'text/xml');
            },
        ],
    ],
]);