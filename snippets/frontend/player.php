<?php

use mauricerenck\Podcaster\Podcast;

$podcastUtils = new Podcast();
$feed = (isset($podcast)) ? $podcast : $podcastUtils->getFeedOfEpisode($page);

if (!$feed) {
    return;
}

switch ($feed->playerType()) {
    case 'podlove':
        snippet('podcaster-podlove-player', ['page' => $page, 'podcast' => $feed]);
        break;
    case 'html5':
        snippet('podcaster-html5-player', ['page' => $page, 'podcast' => $feed]);
        break;
}
