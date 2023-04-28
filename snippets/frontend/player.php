<?php

namespace mauricerenck\Podcaster;

$podcastUtils = new Podcast();
$feed = (isset($feed)) ? $feed : $podcastUtils->getFeedOfEpisode($page);

if (!$feed) {
    return;
}

switch ($feed->playerType()) {
    case 'podlove':
        snippet('podcaster-podlove-player', ['page' => $page]);
        break;
    case 'html5':
        snippet('podcaster-html5-player', ['page' => $page, 'feed' => $feed]);
        break;
}
