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
        $this->assertEquals('/en/episode', $result);
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
        $this->assertEquals('/en/episode', $result);
    }

    public function testGetChapters()
    {
        $pageMock = $this->getPageMock();

        $feed = new Feed();
        $result = $feed->getChapters($pageMock);
        $this->assertCount(3, $result);
    }

    public function testGetAudioFile()
    {
        $pageMock = $this->getPageMock();
        $expected = '/intro-mit-text-kurz.mp3';

        $feed = new Feed();
        $result = $feed->getAudioFile($pageMock);

        $this->assertStringContainsString($expected, $result->url());
    }

    // FIXME check how to mock that fallback file
    //public function testGetAudioFileFallback()
    //{
    //    $pageMock = $this->getPageMock(false, [
    //        'Podcasteraudio' => ''
    //    ]);
    //
    //    $expected = '/audio.mp3';
    //
    //    $feed = new Feed();
    //    $result = $feed->getAudioFile($pageMock);
    //
    //    $this->assertStringContainsString($expected, $result->url());
    //}

    public function testGetAudioEnclosure()
    {
        $feed = new Feed();

        $pageMock = $this->getPageMock();
        $fileMock = $feed->getAudioFile($pageMock);
        $expected = [
            'url' => '/en/episode/download/intro-mit-text-kurz.mp3',
            'length' => 396560,
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
        $expected = '00:00:16';

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

}
