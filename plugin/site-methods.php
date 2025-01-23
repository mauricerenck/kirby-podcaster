<?php

namespace mauricerenck\Podcaster;

return [
    'getAppleMetadata' => function ($endpoint = 'categories') {
        $podcast = new Podcast();
        return $podcast->getAppleMetadata($endpoint);
    }
];
