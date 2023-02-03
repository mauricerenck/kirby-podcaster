<?php

namespace mauricerenck\Podcaster;

use Kirby\Exception\Exception;

return [
    'file.create:after' => function ($file) {
        try {
            $audioUtils = new AudioTools();
            $audioUtils->parseAndWriteId3($file, option('mauricerenck.podcaster.setId3Data', false));
        } catch (Exception $e) {
            throw new Exception(['details' => 'the audio id3 data could not be read']);
        }
    },
    'file.replace:after' => function ($file) {
        try {
            $audioUtils = new AudioTools();
            $audioUtils->parseAndWriteId3($file, option('mauricerenck.podcaster.setId3Data', false));
        } catch (Exception $e) {
            throw new Exception(['details' => 'the audio id3 data could not be read']);
        }
    },
    'system.loadPlugins:after' => function () {
        $migrations = new Migrations();
        $migrations->migrate();
    },
];
