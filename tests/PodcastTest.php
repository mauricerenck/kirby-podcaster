<?php

use mauricerenck\Podcaster\TestCaseMocked;
use \mauricerenck\Podcaster\Podcast;

final class PodcastTest extends TestCaseMocked
{
    public function testGetPageFromSlug()
    {
        $receiverUtils = new Podcast();
        $result = $receiverUtils->getPageFromSlug('phpunit');

        $this->assertEquals('phpunit', $result->slug());
    }

    public function testGetPageFromSlugWithLanguage()
    {
        $senderUtils = new Podcast();
        $result = $senderUtils->getPageFromSlug('de/phpunit');

        $this->assertEquals('phpunit', $result->slug());
    }

    public function testHandleUnknownPageFromSlug()
    {
        $receiverUtils = new Podcast();
        $result = $receiverUtils->getPageFromSlug('invalid');

        $this->assertFalse($result);
    }

    public function testGetAudioFile()
    {
        $pageMock = $this->getPageMock();
        $expected = '/kirby-podcaster-test.mp3';

        $feed = new Podcast();
        $result = $feed->getAudioFile($pageMock);

        $this->assertStringContainsString($expected, $result->url());
    }
    // TODO test getEpisodes()
}
