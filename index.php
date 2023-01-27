<?php

namespace mauricerenck\Podcaster;

use Kirby;

@include_once __DIR__ . '/vendor/autoload.php';

Kirby::plugin('mauricerenck/podcaster', [
    'options' => require_once(__DIR__ . '/internal/options.php'),
    'blueprints' => [
        'pages/podcasterfeed' => __DIR__ . '/blueprints/pages/feed.yml',

        'tabs/podcasterepisode' => __DIR__ . '/blueprints/pages/episode.yml',
        'tabs/podcaster/feed-base' => __DIR__ . '/blueprints/tabs/feed-base.yml',
        'tabs/podcaster/feed-details' => __DIR__ . '/blueprints/tabs/feed-details.yml',
        'tabs/podcaster/feed-stats' => __DIR__ . '/blueprints/tabs/feed-stats.yml',
        'tabs/podcaster/feed-player' => __DIR__ . '/blueprints/tabs/feed-player.yml',
        'tabs/podcaster/feed-tracking' => __DIR__ . '/blueprints/tabs/feed-tracking.yml',

        //'pages/podcasterwizard' => __DIR__ . '/blueprints/pages/podcasterwizard.yml',
        //'files/podcaster-episode' => __DIR__ . '/blueprints/files/podcaster-episode.yml',
        //'files/podcaster-cover' => __DIR__ . '/blueprints/files/podcaster-cover.yml',
    ],
    'templates' => [
        'podcasterfeed' => __DIR__ . '/templates/podcasterfeed.php',
    ],
    'pageMethods' => require_once __DIR__ . '/app/FeedMethods.php',
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
    ]
    //'routes' => [
    //],
]);