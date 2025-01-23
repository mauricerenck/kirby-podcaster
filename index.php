<?php

namespace mauricerenck\Podcaster;

use Kirby\Cms\App as Kirby;

@include_once __DIR__ . '/vendor/autoload.php';

Kirby::plugin('mauricerenck/podcaster', [
    'options' => require_once(__DIR__ . '/plugin/options.php'),
    'blueprints' => require_once(__DIR__ . '/plugin/blueprints.php'),
    'templates' => [
        'podcasterfeed' => __DIR__ . '/templates/podcasterfeed.php',
    ],
    'pageMethods' => require_once __DIR__ . '/plugin/page-methods.php',
    'siteMethods' => require_once __DIR__ . '/plugin/site-methods.php',
    'fileTypes' => require_once __DIR__ . '/plugin/file-types.php',
    'api' => require_once(__DIR__ . '/plugin/api.php'),
    'hooks' => require_once(__DIR__ . '/plugin/hooks.php'),
    'areas' => require_once(__DIR__ . '/plugin/areas.php'),
    'snippets' => require_once(__DIR__ . '/plugin/snippets.php'),
    'routes' => require_once(__DIR__ . '/plugin/routes.php'),
]);
