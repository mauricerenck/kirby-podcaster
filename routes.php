<?php
return [
    [
      'pattern' => '(:all)/download/(:any)',
      'action'  => function ($slug, $filename) {
        $episode = page($slug);
            $filename = str_replace('.mp3', '', $filename);
            return  $episode->audio($episode->podcasterMp3())->first();
            return $file;
      }
    ]
];