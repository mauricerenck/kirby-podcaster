<?php

namespace mauricerenck\Podcaster;

use Kirby;
use Kirby\Http\Response;

@include_once __DIR__ . '/vendor/autoload.php';

Kirby::plugin('mauricerenck/podcaster', [
    'options' => require_once(__DIR__ . '/internal/options.php'),
    'templates' => [
        'podcasterfeed' => __DIR__ . '/templates/podcasterfeed.php',
    ],
    'pageMethods' =>  require_once __DIR__ . '/app/FeedMethods.php',
    'snippets' => [
        'podcaster-feed-header' => __DIR__ . '/snippets/xml/xml-header.php',
        'podcaster-feed-cover' => __DIR__ . '/snippets/xml/channel-cover.php',
        'podcaster-feed-author' => __DIR__ . '/snippets/xml/channel-author.php',
        'podcaster-feed-owner' => __DIR__ . '/snippets/xml/channel-owner.php',
        'podcaster-feed-description' => __DIR__ . '/snippets/xml/channel-description.php',
        'podcaster-feed-categories' => __DIR__ . '/snippets/xml/channel-categories.php',
    ]
    //'routes' => [
    //],
]);