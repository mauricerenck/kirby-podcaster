<?php

use PHPUnit\Framework\TestCase;
use \mauricerenck\Podcaster\Podcast;

final class PodcastTest extends TestCase
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

    // TODO test getEpisodes()
}
