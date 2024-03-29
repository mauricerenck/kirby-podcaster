<?php

use mauricerenck\Podcaster\TestCaseMocked;
use \mauricerenck\Podcaster\Feed;

final class FeedTest extends TestCaseMocked
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
        $testValue = 'This is <a href="#">A test</a>';
        $feed = new Feed();
        $result = $feed->xmlTag('link', $testValue, true);
        $this->assertEquals('<link><![CDATA[' . $testValue . ']]></link>', $result);
    }

    public function testCreateXmlTagWithoutFieldValue()
    {
        $feed = new Feed();
        $result = $feed->xmlTag('link', null, true);
        $this->assertEquals('<link/>', $result);
    }

    public function testCreateXmlTagWithoutFieldValueWithAttr()
    {
        $feed = new Feed();
        $result = $feed->xmlTag('link', null, true, ['attr' => 'value']);
        $this->assertEquals('<link attr="value"/>', $result);
    }

    public function testCreateXmlTagWithEverything()
    {
        $feed = new Feed();
        $result = $feed->xmlTag('link', 'value', true, ['attr' => 'value']);
        $this->assertEquals('<link attr="value"><![CDATA[value]]></link>', $result);
    }

    public function testCreateXmlTagHandleInvalidValue()
    {
        $feed = new Feed();
        $result = $feed->xmlTag('link', false, true, ['attr' => 'value']);
        $this->assertEquals('', $result);
    }

    public function testGetGuidWithPermalink()
    {
        $pageMock = $this->getPageMock();
        $feed = new Feed();
        $result = $feed->getGuid($pageMock, false);
        $this->assertEquals('58d1bc31042b9e873661db17ff2c1822', $result);
    }

    public function testGetGuidWithUuid()
    {
        $pageMock = $this->getPageMock();
        $feed = new Feed();
        $result = $feed->getGuid($pageMock, true);
        $this->assertEquals($pageMock->permalink(), $result);
    }

    public function testGetGuidWithFallback()
    {
        $pageMock = $this->getPageMock();
        $feed = new Feed();
        $result = $feed->getGuid($pageMock, null);
        $this->assertEquals('58d1bc31042b9e873661db17ff2c1822', $result);
    }

    public function testGetChapters()
    {
        $pageMock = $this->getPageMock();

        $feed = new Feed();
        $result = $feed->getChapters($pageMock);
        $this->assertCount(3, $result);
    }

    public function testGetAudioEnclosure()
    {
        $feed = new Feed();

        $pageMock = $this->getPageMock();
        $fileMock = $feed->getAudioFile($pageMock);
        $expected = [
            'url' => '/en/episode/download/kirby-podcaster-test.mp3',
            'length' => 481406,
            'type' => 'audio/mpeg',
        ];

        $result = $feed->getAudioEnclosures($pageMock, $fileMock);

        $this->assertEquals($expected, $result);
    }

    public function testGetAudioDuration()
    {
        $feed = new Feed();

        $pageMock = $this->getPageMock();
        $fileMock = $feed->getAudioFile($pageMock);

        $result = $feed->getAudioDuration($fileMock);
        $expected = '00:00:15';

        $this->assertEquals($expected, $result);
    }

    public function testGetCoverFile()
    {
        $pageMock = $this->getPageMock();
        $expected = '/cover.png';

        $feed = new Feed();
        $result = $feed->getCoverFile($pageMock);

        $this->assertStringContainsString($expected, $result->url());
    }

    public static function tearDownAfterClass(): void
    {
        if (file_exists('content/episode')) {
            unlink('content/episode/episode.en.txt');
            rmdir('content/episode');
        }
    }

    public function testGetTranscript()
    {
        $pageMock = $this->getPageMock();

        $feed = new Feed();
        $result = $feed->getTranscript($pageMock);
        $this->assertCount(2, $result);
    }
}
