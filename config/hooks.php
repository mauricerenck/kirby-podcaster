<?php

namespace Plugin\Podcaster;

return [
    'file.create:after' => function ($file) {
        if ($file->extension() == 'mp3') {
            try {
                $audioUtils = new PodcasterAudioUtils();
                $id3 = $audioUtils->getAudioMeta($file);
                $audioUtils->setAudioFileMeta($id3, $file);

                if (option('mauricerenck.podcaster.setId3Data')) {
                    $audioUtils->setAudioMetaToPage($id3, $file);
                }
            } catch (Exception $e) {
                throw new Exception(['details' => 'the audio id3 data could not be read']);
            }
        }
    },
    'file.replace:after' => function ($file) {
        if ($file->extension() == 'mp3') {
            try {
                $audioUtils = new PodcasterAudioUtils();
                $id3 = $audioUtils->getAudioMeta($file);
                $audioUtils->setAudioFileMeta($id3, $file);

                if (option('mauricerenck.podcaster.setId3Data')) {
                    $audioUtils->setAudioMetaToPage($id3, $file);
                }
            } catch (Exception $e) {
                throw new Exception(['details' => 'the audio id3 data could not be read']);
            }
        }
    }
];
