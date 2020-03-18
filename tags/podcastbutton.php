<?php
return [
    'attr' => [
        'rss',
        'itunes',
        'label',
        'classes'
    ],
    'html' => function ($tag) {
        $rss = $tag->attr('rss');
        $podcast = page($rss);
        $itunes = $tag->attr('itunes', '');
        $label = $tag->attr('podcastbutton', 'subscribe');
        $classes = $tag->attr('classes', 'button button-primary button-tiny');

        $output = [];
        $output[] = '<button class="podlove-subscribe-button-podcast ">' . $label . '</button>';
        $output[] = '<script>';

        $output[] = 'window.podcastData = {';
        $output[] = '"title": "' . $podcast->podcasterTitle()->html() . '",';
        $output[] = '"subtitle": "' . $podcast->podcasterSubtitle()->html() . '",';
        $output[] = '"description": "' . $podcast->podcasterDescription()->html() . '",';
        if ($podcast->podcasterCover()->isNotEmpty()) :
                $output[] = '"cover": "' . $podcast->podcasterCover()->toFile()->resize(200)->url() . '",';
        endif;

        $output[] = '"feeds": [';
        $output[] = '{';
        $output[] = '"type": "audio",';
        $output[] = '"format": "mp3",';
        $output[] = '"url": "' . url($rss) . '",';
        $output[] = '"variant": "high",';
        $output[] = '"directory-url-itunes": "' . $itunes . '"';
        $output[] = '}';
        $output[] = ']';
        $output[] = '}';
        $output[] = '</script>';

        $output[] = '<script';
        $output[] = 'class="podlove-subscribe-button"';
        $output[] = 'src="https://cdn.podlove.org/subscribe-button/javascripts/app.js"';
        $output[] = 'data-buttonid="podcast"';
        $output[] = 'data-hide="true"';
        $output[] = 'data-language="' . kirby()->language()->code() . '"';
        $output[] = 'data-size="small"';
        $output[] = 'data-json-data="podcastData"';
        $output[] = 'data-colors="#07708a;#9dbf16;#0082d5"';
        $output[] = 'data-hide="true"></script>';

        $output[] = '<noscript>';
        $output[] = '<a href="' . $itunes . '" class="button button-primary button-tiny">iTunes</a>';
        $output[] = '<a href="' . url($rss) . '" class="button button-primary button-tiny">RSS</a>';
        $output[] = '</noscript>';

        return implode(' ', $output);
    }
];
