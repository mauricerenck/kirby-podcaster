<?php

use mauricerenck\Podcaster\AudioTools;
use mauricerenck\Podcaster\TestCaseMocked;

final class AudioToolsTest extends TestCaseMocked
{
    //public function testPodcasterTitle()
    //{
    //    $pageMock = $this->getPageMock();
    //    $fileMock = $feed->getAudioFile($pageMock);
    //}

    public function testConvertAudioDurations() {
        $audioTools = new AudioTools();
        $durationSeconds = $audioTools->convertAudioDuration(10);
        $durationMinutes = $audioTools->convertAudioDuration(70);
        $durationHours = $audioTools->convertAudioDuration(3670);

        $this->assertEquals('00:00:10', $durationSeconds);
        $this->assertEquals('00:01:10', $durationMinutes);
        $this->assertEquals('01:01:10', $durationHours);
    }

    public static function tearDownAfterClass(): void
    {
        if (file_exists('content/episode')) {
            unlink('content/episode/episode.en.txt');
            rmdir('content/episode');
        }
    }
}
