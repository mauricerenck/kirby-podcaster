<?php

use mauricerenck\Podcaster\TestCaseMocked;
use mauricerenck\Podcaster\Podcast;

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

        $podcast = new Podcast();
        $result = $podcast->getAudioFile($pageMock);

        $this->assertStringContainsString($expected, $result->url());
    }
    // TODO test getEpisodes()

    public function testGetPodloveRolesWhenEpisodeIsBogus()
    {
        $expected = [
            [
                'id' => '1',
                'title' => 'Team',
            ],
        ];

        $podcast = new Podcast();
        $result = $podcast->getPodloveRoles('not/existing');

        $this->assertEquals($expected, $result);
    }

    public function testGetPodloveRolesWhenPageHasNoFeed()
    {
        $expected = [
            [
                'id' => '1',
                'title' => 'Team',
            ],
        ];

        $podcast = new Podcast();
        $result = $podcast->getPodloveRoles('phpunit');

        $this->assertEquals($expected, $result);
    }

    public function testGetPodloveRolesWhenNoneSet()
    {
        $expected = [
            [
                'id' => '1',
                'title' => 'Team',
            ],
        ];

        $podcast = new Podcast();
        $result = $podcast->getPodloveRoles('phpunit/podcast-flat/episode-1');

        $this->assertEquals($expected, $result);
    }

    public function testGetPodloveRoles()
    {
        $expected = [
            [
                'id' => '1',
                'title' => 'Team Role',
            ],
        ];

        $podcast = new Podcast();
        $result = $podcast->getPodloveRoles('phpunit/podcast-seasons/season01/episode-1');

        $this->assertEquals($expected, $result);
    }

    public function testGetPodloveGroupsWhenEpisodeIsBogus()
    {
        $expected = [
            [
                'id' => '1',
                'title' => 'Team',
            ],
        ];

        $podcast = new Podcast();
        $result = $podcast->getPodloveGroups('not/existing');

        $this->assertEquals($expected, $result);
    }

    public function testGetPodloveGroupsWhenPageHasNoFeed()
    {
        $expected = [
            [
                'id' => '1',
                'title' => 'Team',
            ],
        ];

        $podcast = new Podcast();
        $result = $podcast->getPodloveGroups('phpunit');

        $this->assertEquals($expected, $result);
    }

    public function testGetPodloveGroupsWhenNoneSet()
    {
        $expected = [
            [
                'id' => '1',
                'title' => 'Team',
            ],
        ];

        $podcast = new Podcast();
        $result = $podcast->getPodloveGroups('phpunit/podcast-flat/episode-1');

        $this->assertEquals($expected, $result);
    }

    public function testGetPodloveGroups()
    {
        $expected = [
            [
                'id' => '1',
                'title' => 'On Air',
            ],
        ];

        $podcast = new Podcast();
        $result = $podcast->getPodloveGroups('phpunit/podcast-seasons/season01/episode-1');

        $this->assertEquals($expected, $result);
    }

    public function testGetContributors()
    {
        $pageMock = page('phpunit/podcast-seasons/season01/episode-1');
        $feedMock = page('phpunit/podcast-seasons/feed');

        $expected = [
            [
                'id' => '1',
                'name' => 'Maurice Renck',
                'avatar' => 'https://test.tld/avatar.png',
                'role' => [
                    'id' => 1,
                    'slug' => 'team-role',
                    'title' => 'Team Role',
                ],
                'group' => [
                    'id' => 1,
                    'slug' => 'on-air',
                    'title' => 'On Air',
                ],
            ],
        ];

        $podcast = new Podcast();
        $result = $podcast->getPodloveContributors(
            $pageMock->podcasterContributors(),
            $feedMock->podcasterPodloveRoles(),
            $feedMock->podcasterPodloveGroups()
        );

        $this->assertEquals($expected, $result);
    }

    public function testPodloveEpisodeJson()
    {
        $pageMock = page('phpunit/podcast-seasons/season01/episode-1');

        $expected = [
            'version' => 5,
            'show' => [
                'title' => 'Test Podcast',
                'subtitle' => 'Podcast Subtitle',
                'summary' =>
                'Lorem ipsum dolor sit amet, consetetur sadipscing elitr, sed diam nonumy eirmod tempor invidunt ut labore et dolore magna aliquyam erat, sed diam voluptua. At vero eos et accusam et justo duo dolores et ea rebum. Stet clita kasd gubergren, no sea takimata sanctus est Lorem ipsum dolor sit amet. Lorem ipsum dolor sit amet, consetetur sadipscing elitr, sed diam nonumy eirmod tempor invidunt ut labore et dolore magna aliquyam erat, sed diam voluptua. At vero eos et accusam et justo duo dolores et ea rebum. Stet clita kasd gubergren, no sea takimata sanctus est Lorem ipsum dolor sit amet.',
                'poster' => '/media/pages/phpunit/podcast-seasons/feed/2b6f862a69-1752141852/cover.png',
                'link' => 'https://erzaehl.es',
            ],

            'title' => 'Episode Title',
            'subtitle' => 'Episode subtitle',
            'summary' =>
            'Lorem ipsum dolor sit amet, consetetur sadipscing elitr, sed diam nonumy eirmod tempor invidunt ut labore et dolore magna aliquyam erat, sed diam voluptua. At vero eos et accusam et justo duo dolores et ea rebum. Stet clita kasd gubergren, no sea takimata sanctus est Lorem ipsum dolor sit amet. Lorem ipsum dolor sit amet, consetetur sadipscing elitr, sed diam nonumy eirmod tempor invidunt ut labore et dolore magna aliquyam erat, sed diam voluptua. At vero eos et accusam et justo duo dolores et ea rebum. Stet clita kasd gubergren, no sea takimata sanctus est Lorem ipsum dolor sit amet.',
            'publicationDate' => '2021-01-06T21:45:00+01:00',
            'duration' => '00:00:15',
            'poster' => '/media/pages/phpunit/podcast-seasons/feed/2b6f862a69-1752141852/cover.png',
            'link' => $pageMock->url(),

            'audio' => [
                [
                    'url' => '/de/phpunit/podcast-seasons/season01/episode-1/download/kirby-podcaster-test.mp3',
                    'size' => '481406',
                    'title' => 'MP3 Audio',
                    'mimeType' => 'audio/mpeg',
                ],
            ],

            'files' => [
                [
                    'url' => '/de/phpunit/podcast-seasons/season01/episode-1/download/kirby-podcaster-test.mp3',
                    'size' => '481406',
                    'title' => 'MP3 Audio',
                    'mimeType' => 'audio/mpeg',
                ],
            ],

            'chapters' => [
                [
                    'start' => '00:10:00.000',
                    'title' => 'Chapter Title',
                    'href' => 'https://chapter.tld',
                    'image' =>
                    '/media/pages/phpunit/podcast-seasons/season01/episode-1/b6bd54d3b2-1752141852/cover.png',
                ],
                [
                    'start' => '00:20:00.000',
                    'title' => 'Second Chapter with ä ö ü ß',
                    'href' => 'https://chapter.tld',
                    'image' => '',
                ],
                [
                    'start' => '00:00:30.000',
                    'title' => 'chapter3',
                    'href' => '',
                    'image' => '',
                ],
            ],

            'contributors' => [
                [
                    'id' => '1',
                    'name' => 'Maurice Renck',
                    'avatar' => 'https://test.tld/avatar.png',
                    'role' => [
                        'id' => 1,
                        'slug' => 'team-role',
                        'title' => 'Team Role',
                    ],
                    'group' => [
                        'id' => 1,
                        'slug' => 'on-air',
                        'title' => 'On Air',
                    ],
                ],
            ],
            'transcripts' => '/media/pages/phpunit/podcast-seasons/season01/episode-1/df30ca4d51-1752141852/test.vtt',
        ];

        $podcast = new Podcast();
        $result = $podcast->getPodloveEpisodeJson($pageMock);

        $this->assertEquals($expected, $result);
    }

    public function testPodloveConfigJson()
    {
        $pageMock = page('phpunit/podcast-seasons/season01/episode-1');

        $expected = [
            'version' => 5,
            'base' => 'player/',
            'activeTab' => 'chapters',

            'theme' => [
                'tokens' => [
                    'brand' => '#166255',
                    'brandDark' => '#166255',
                    'brandDarkest' => '#1A3A4A',
                    'brandLightest' => '#E5EAEC',
                    'shadeDark' => '#807E7C',
                    'shadeBase' => '#807E7C',
                    'contrast' => '#000',
                    'alt' => '#fff',
                ],
                'fonts' => [
                    'ci' => [
                        'name' => 'RobotoBlack',
                        'family' => ['RobotoBlack', 'Calibri', 'Candara', 'Arial', 'Helvetica', 'sans-serif'],
                        'weight' => 900,
                        'src' => ['./assets/Roboto-Black.ttf'],
                    ],
                    'regular' => [
                        'name' => 'FiraSansLight',
                        'family' => ['FiraSansLight', 'Calibri', 'Candara', 'Arial', 'Helvetica', 'sans-serif'],
                        'weight' => 300,
                        'src' => ['./assets/FiraSans-Light.ttf'],
                    ],
                    'bold' => [
                        'name' => 'FiraSansBold',
                        'family' => ['FiraSansBold', 'Calibri', 'Candara', 'Arial', 'Helvetica', 'sans-serif'],
                        'weight' => 700,
                        'src' => ['./assets/FiraSans-Bold.ttf'],
                    ],
                ],
            ],
            'subscribe-button' => [
                'feed' => '/de/phpunit/podcast-seasons/feed',
                'clients' => [
                    [
                        'id' => 'apple-podcasts',
                        'service' => '12345678',
                    ],
                    [
                        'id' => 'pocket-casts',
                        'service' => '/de/phpunit/podcast-seasons/feed',
                    ],
                    [
                        'id' => 'overcast',
                    ],
                    [
                        'id' => 'rss',
                    ],
                ],
            ],
            'share' => [
                'channels' => ['facebook', 'twitter', 'whats-app', 'linkedin', 'pinterest', 'xing', 'mail', 'link'],
                'sharePlaytime' => true,
                'outlet' => '/de/phpunit/podcast-seasons/season01/episode-1',
            ],
        ];

        $podcast = new Podcast();
        $result = $podcast->getPodloveConfigJson($pageMock);

        $this->assertEquals($expected, $result);
    }
}
