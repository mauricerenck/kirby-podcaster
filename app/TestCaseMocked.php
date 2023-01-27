<?php

namespace mauricerenck\Podcaster;

use Kirby\Cms\File;
use Kirby\Cms\Files;
use Kirby\Cms\Page;
use PHPUnit\Framework\TestCase;

class TestCaseMocked extends TestCase
{
    function getPageMock($draft = false, $content = [])
    {
        $defaultContent = [
            'Podcasteraudio' => '- file://E0kamkOlNZehbYkm',
            'Podcastermp3' => '- file://E0kamkOlNZehbYkm',
            'Podcastercover' => '- file://NxwPC4OF9isofI1h',
            'Podcasterchapters' => '- 
  podcasterchaptertimestamp: 00:10:00
  podcasterchaptertitle: Chapter Title
  podcasterchapterurl: https://chapter.tld
  podcasterchapterimage:
    - file://NxwPC4OF9isofI1h
- 
  podcasterchaptertimestamp: 00:20:00
  podcasterchaptertitle: Second Chapter
  podcasterchapterurl: https://chapter.tld
  podcasterchapterimage: [ ]
- 
  podcasterchaptertimestamp: 00:00:30
  podcasterchaptertitle: chapter3
  podcasterchapterurl: ""
  podcasterchapterimage: [ ]
  ',
        ];

        $pageContent = array_merge($defaultContent, $content);

        $pageMock = Page::factory([
                                      'blueprint' => ['episode'],
                                      'content' => $pageContent,
                                      'dirname' => 'episode',
                                      'slug' => 'episode',
                                      'isDraft' => $draft,
                                      'template' => 'episode',
                                  ]);

        //File::factory([
        //                  'parent' => $pageMock,
        //                  'filename' => 'audio.mp3',
        //                  'content' => [''],
        //              ]);

        return $pageMock;
    }
}