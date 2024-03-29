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
      'Podcasteraudio' => '- file://sFzaPs7cI1STHfWS',
      'Podcastermp3' => '- file://sFzaPs7cI1STHfWS',
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
  'Podcastertranscript' => '- 
    podcastertranscriptlanguage: ar-KW
    podcastertranscriptfile:
      - file://Y1eW2AZeVUOh11pl
- 
  podcastertranscriptlanguage: de
  podcastertranscriptfile:
    - file://L10JTYu3jhtHgO3e
    '
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
