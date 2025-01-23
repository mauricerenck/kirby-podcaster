<?php

return [
    'pages/podcasterfeed' => __DIR__ . '/../blueprints/pages/feed.yml',

    'tabs/podcasterepisode' => __DIR__ . '/../blueprints/tabs/episode.yml', // backwards compatibility
    'tabs/podcaster/episode' => __DIR__ . '/../blueprints/tabs/episode.yml',
    'tabs/podcaster/feed-base' => __DIR__ . '/../blueprints/tabs/feed-base.yml',
    'tabs/podcaster/feed-details' => __DIR__ . '/../blueprints/tabs/feed-details.yml',
    'tabs/podcaster/feed-stats' => __DIR__ . '/../blueprints/tabs/feed-stats.yml',
    'tabs/podcaster/feed-player' => __DIR__ . '/../blueprints/tabs/feed-player.yml',
    'tabs/podcaster/feed-tracking' => __DIR__ . '/../blueprints/tabs/feed-tracking.yml',

    'files/podcaster-episode' => __DIR__ . '/../blueprints/files/podcaster-episode.yml',
    'files/podcaster-cover' => __DIR__ . '/../blueprints/files/podcaster-cover.yml',

    'sections/podcastermp3' => function () {
        return (option('mauricerenck.podcaster.compatibilityMode', false)) ? __DIR__ . '/../blueprints/sections/podcastermp3.yml' : __DIR__ . '/../blueprints/sections/empty.yml';
    },
    'sections/podcasterimage' => function () {
        return (option('mauricerenck.podcaster.compatibilityMode', false)) ? __DIR__ . '/../blueprints/sections/podcasterimage.yml' : __DIR__ . '/../blueprints/sections/empty2.yml';
    },
];
