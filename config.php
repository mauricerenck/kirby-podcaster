<?php
namespace Plugin\Podcast;
use Kirby;
use Kirby\Exception\Exception;

Kirby::plugin('mauricerenck/podcaster', [
    'options' => [
        'downloadTriggerPath' => 'download'
    //     'widgetEntries' => 10,
    //     'piwikBase' => null,
    //     'piwikId' => null,
    //     'piwikToken' => '',
    //     'piwikGoalId' => null,
    //     'piwikEventName' => 'Download',
    //     'piwikAction' => false,
    //     'GAUA' => false, 
    //     'GA.pageView' => false,
    //     'GA.eventName' => 'Download',
    ],
    'templates' => [
        'podcasterfeed' => __DIR__ . '/templates/podcasterfeed.php'
    ],
    'blueprints' => [
        'pages/podcasterfeed' => __DIR__ . '/blueprints/pages/podcasterfeed.yml',
        'tabs/podcasterepisode' => __DIR__ . '/blueprints/tabs/episode.yml'
    ],
    'hooks' => [
        'file.create:after' => function ($file) {
            if($file->isAudio()) {
                try {
                    $audioUtils = new PodcasterAudioUtils();
                    $audioUtils->setAudioFileMeta($file);
                } catch(Exception $e) {
                    throw new Exception(array('details' => 'the audio id3 data could not be read'));
                }
            }
        },
        'file.replace:after' => function ($file) {
            if ($file->isAudio()) {
                try {
                    $audioUtils = new PodcasterAudioUtils();
                    $audioUtils->setAudioFileMeta($file);
                } catch (Exception $e) {
                    throw new Exception(array('details' => 'the audio id3 data could not be read'));
                }
            }
        }
    ]
]);