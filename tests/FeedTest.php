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

    public function testCreateXmlTag()
    {
        $feed = new Feed();
        $result = $feed->xmlTag('link', 'https://link.tld');
        $this->assertEquals('<link>https://link.tld</link>', $result);
    }

    public function testCreateXmlTagWithCData()
    {
        $feed = new Feed();
        $result = $feed->xmlTag('link', 'This is <a href="#">A test</a>', true);
        $this->assertEquals('<link><![CDATA[This is <a href="#">A test</a>]]></link>', $result);
    }

        public function testCreateXmlTagWithoutFieldValue()
    {
        $feed = new Feed();
        $result = $feed->xmlTag('link', null, true);
        $this->assertEquals('', $result);
    }
}
