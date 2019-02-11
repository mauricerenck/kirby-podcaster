<?php
    $podcast = (isset($podcast)) ? $podcast : $page->siblings()->find('feed');

    switch($podcast->playerType()) {
        case 'podlove':
            snippet('podcaster-podlove-player', ['page' => $page, 'podcast' => $podcast]);
            break;
        case 'html5':
            snippet('podcaster-html5-player', ['page' => $page, 'podcast' => $podcast]);
            break;
    }
?>