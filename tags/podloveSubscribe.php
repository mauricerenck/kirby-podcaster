<?php

namespace mauricerenck\Podcaster;

return [
    'attr' => [
        'feed',
        'itunesUrl',
        'label',
        'lang',
        'buttonClass',
    ],
    'html' => function ($tag) {
        $id = $tag->attr('podloveSubscribe');
        $feed = $tag->attr('feed');
        $label = $tag->attr('label');
        $lang = $tag->attr('lang');
        $itunesUrl = $tag->attr('itunesUrl');
        $buttonClass = $tag->attr('buttonClass');
        $podcast = page($feed);

        $podcastId = $podcast->podcastId();
        $podcastTitle = $podcast->podcasterTitle()->html();
        $subtitle = $podcast->podcasterSubtitle()->html();
        $podcastDescription = str_replace("\n", ' ', $podcast->podcasterDescription()->markdown());
        $podcastCover = $podcast->podcasterCover()->isNotEmpty() ? $podcast->podcasterCover()->toFile()->resize(200)->url() : '';
        $podcastUrl = url($podcast);
        $podcastColor = str_replace('#', '', $podcast->podcasterPodloveMainColor());

        $html = <<<EOT
            <button class="podlove-subscribe-button-$podcastId $buttonClass">$label</button>
            <script>
            window.podcastData = {
                "title": "$podcastTitle",
                "subtitle": "$subtitle",
                "description": "$podcastDescription",
                "cover": "$podcastCover",
                "feeds": [{
                    "type": "audio",
                    "format": "mp3",
                    "url": "$podcastUrl",
                    "variant": "high",
                    "directory-url-itunes": "$itunesUrl"
                }]
            }
            </script>
            <script class="podlove-subscribe-button" src="https://cdn.podlove.org/subscribe-button/javascripts/app.js" data-buttonid="$podcastId" data-hide="true" data-language="$lang" data-size="small" data-json-data="podcastData" data-colors="#$podcastColor;" data-hide="true"></script>
            EOT;

        return $html;
    }
];
