<?php

use PHPUnit\Framework\TestCase;
use \mauricerenck\Podcaster\Feed;

final class FeedTest extends TestCase
{

    public function testPodcasterTitle()
    {
        $page = page('phpunit/podcast-flat/feed');
        $result = $page->title();

        $this->assertEquals('feed', $result);
    }


}
