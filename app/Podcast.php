<?php

namespace mauricerenck\Podcaster;

use Exception;
use Kirby\Cms\Collection;
use Kirby\Cms\Structure;
use Kirby\Filesystem\F;
use Kirby\Toolkit\Str;
use Kirby\Http\Remote;

class Podcast
{
    public function getPageFromSlug(string $slug)
    {
        if ($slug == '') {
            $page = page(site()->homePageId());
        } elseif (!$page = page($slug)) {
            $page = page(kirby()->router()->call($slug));

            if ($page->isHomeOrErrorPage()) {
                return false;
            }
        }

        if (is_null($page)) {
            return false;
        }

        return $page;
    }

    public function getPodcastFromId(string $id)
    {
        $pages = site()->index()->filterBy('template', 'podcasterfeed')->filter(function ($child) use ($id) {
            return $child->podcastId()->value() === $id;
        })->first();


        if (is_null($pages)) {
            return false;
        }

        return $pages;
    }

    public function getFeedOfEpisode($episode)
    {
        $feed = $episode->siblings()->filterBy('intendedTemplate', 'podcasterfeed')->first();

        if (is_null($feed) || $feed->count() === 0) {
            $parent = $episode->parent();
            if (!isset($parent)) {
                return null;
            }

            $grandParent = $parent->parent();

            if (!isset($grandParent)) {
                return null;
            }

            $feed = $grandParent->children()->filterBy('intendedTemplate', 'podcasterfeed')->first();
        }

        return $feed;
    }

    public function getEpisodes($rssFeed)
    {
        return $rssFeed->podcasterSource()
            ->toPages()
            ->children()
            ->listed()
            ->filter(function ($child) {
                return $child->date()->toDate() <= time();
            })
            ->filter(function ($child) {
                return $child->hasAudio();
            })->sortBy('date', 'desc');
    }

    public function getAudioFile($episode)
    {
        if ($episode->podcasterAudio()->isNotEmpty()) {
            return $episode->podcasterAudio()->toFile();
        }

        if (!is_null($episode->podcasterMp3())) {
            $audioFile = $episode->audio($episode->podcasterMp3()->first())->first();

            if (is_null($audioFile)) {
                return null;
            }

            return $audioFile;
        }

        $audioFile = $episode->audio()->first();

        if (is_null($audioFile)) {
            return null;
        }

        return $audioFile;
    }

    public function getAllPodcasts(): array
    {
        $pagesWithPodcastFeed = site()->index()->filterBy('template', 'podcasterfeed');

        $podcasts = [];
        foreach ($pagesWithPodcastFeed as $podcast) {
            $podcasts[] = ['id' => $podcast->podcastId()->value(), 'name' => $podcast->podcasterTitle()->value()];
        }

        return $podcasts;
    }

    public function getPluginVersion()
    {
        try {
            $composerString = F::read(__DIR__ . '/../composer.json');
            $composerJson = json_decode($composerString);

            return $composerJson->version;
        } catch (Exception $e) {
            return 'Unknown';
        }
    }

    public function getPodloveRoles($episodeSlug)
    {
        $episode = $this->getPageFromSlug($episodeSlug);
        if (!$episode || is_null($episode)) {
            return [
                [
                    'id' => '1',
                    'title' => 'Team',
                ]
            ];
        }

        $feed = $this->getFeedOfEpisode($episode);
        if (!$feed || is_null($feed)) {
            return [
                [
                    'id' => '1',
                    'title' => 'Team',
                ]
            ];
        }

        if ($feed->podcasterPodloveRoles()->isEmpty()) {
            return [
                [
                    'id' => '1',
                    'title' => 'Team',
                ]
            ];
        }

        $podloveRoles = $feed->podcasterPodloveRoles()->toStructure();
        $roles = [];

        foreach ($podloveRoles as $role) {
            $roles[] = [
                'id' => (string)$role->roleId()->value(),
                'title' => $role->roleTitle()->value(),
            ];
        }

        return $roles;
    }

    public function getPodloveGroups($episodeSlug)
    {
        $episode = $this->getPageFromSlug($episodeSlug);
        if (!$episode || is_null($episode)) {
            return [
                [
                    'id' => '1',
                    'title' => 'Team',
                ]
            ];
        }

        $feed = $this->getFeedOfEpisode($episode);

        if (!$feed || is_null($feed)) {
            return [
                [
                    'id' => '1',
                    'title' => 'Team',
                ]
            ];
        }

        if ($feed->podcasterPodloveRoles()->isEmpty()) {
            return [
                [
                    'id' => '1',
                    'title' => 'Team',
                ]
            ];
        }

        $podloveRoles = $feed->podcasterPodloveGroups()->toStructure();
        $roles = [];

        foreach ($podloveRoles as $role) {
            $roles[] = [
                'id' => (string)$role->roleId()->value(),
                'title' => $role->roleTitle()->value(),
            ];
        }

        return $roles;
    }

    public function getPodloveContributors($contributorsField, $contributorRoles, $contributorGroups)
    {
        if ($contributorRoles->toStructure()->isEmpty() || $contributorGroups->toStructure()->isEmpty()) {
            return [];
        }

        $contributors = [];
        $roles = $contributorRoles->toStructure();
        $groups = $contributorGroups->toStructure();

        foreach ($contributorsField->toStructure() as $contributor) {
            $roleData = null;
            foreach ($roles as $role) {
                if ($role->roleId()->value() == $contributor->contributorRole()->value()) {
                    $roleData = $role;
                }
            }

            $groupData = null;
            foreach ($groups as $group) {
                if ($group->roleId()->value() == $contributor->contributorGroup()->value()) {
                    $groupData = $group;
                }
            }

            $contributors[] = [
                'id' => $contributor->contributorId()->value(),
                'name' => $contributor->contributorName()->value(),
                'avatar' => $contributor->contributorAvatar()->value(),
                'role' => [
                    'id' => $roleData->roleId()->value(),
                    'title' => $roleData->roleTitle()->value(),
                    'slug' => Str::slug($roleData->roleTitle()->value()),
                ],
                'group' => [
                    'id' => $groupData->groupId()->value(),
                    'title' => $groupData->groupTitle()->value(),
                    'slug' => Str::slug($groupData->groupTitle()->value()),
                ],
            ];
        }

        return $contributors;
    }

    public function getPodloveFonts($feed)
    {
        if ($feed->podcasterPodloveFonts()->isEmpty()) {
            return null;
        }

        $fonts = $feed->podcasterPodloveFonts()->toStructure();
        $podloveFonts = [];
        foreach ($fonts as $font) {
            $podloveFont = [
                'name' => $font->name()->value(),
                'family' => $font->family()->split(),
                'weight' => $font->weight()->value(),
                'src' => $font->src()->split(),
            ];

            switch ($font->fontType()->value()) {
                case 'ci':
                    $podloveFonts['ci'] = $podloveFont;
                case 'regular':
                    $podloveFonts['regular'] = $podloveFont;
                case 'bold':
                    $podloveFonts['bold'] = $podloveFont;
            }
        }

        return $podloveFonts;
    }

    public function getPodloveColors($feed)
    {
        if ($feed->podcasterPodloveColors()->isEmpty()) {
            return null;
        }

        $colors = $feed->podcasterPodloveColors()->toStructure();
        $podloveColors = [];
        foreach ($colors as $color) {
            $colorType = $color->colorType()->value();
            $podloveColors[$colorType] = $color->hex()->value();
        }

        return $podloveColors;
    }

    public function getPodloveClients($feed)
    {
        if ($feed->podcasterPodloveClients()->isEmpty()) {
            return null;
        }

        $clients = $feed->podcasterPodloveClients()->toStructure();
        $podloveClients = [];
        foreach ($clients as $client) {
            $podloveClient = [
                'id' => $client->client()->value(),
            ];

            if ($client->service()->isNotEmpty()) {
                $podloveClient['service'] = $client->service()->value();
            }

            switch ($client->client()->value()) {
                case 'google-podcasts':
                    $podloveClient['service'] = $feed->url();
                    break;
                case 'pocket-casts':
                    $podloveClient['service'] = $feed->url();
                    break;
            }

            $podloveClients[] = $podloveClient;
        }

        return $podloveClients;
    }

    public function getPodloveSharing($feed, $episode)
    {
        if ($feed->podcasterPodloveShareChannels()->isEmpty()) {
            return null;
        }

        return [
            'channels' => $feed->podcasterPodloveShareChannels()->split(),
            'sharePlaytime' => $feed->podcasterPodloveSharePlaytime()->isTrue(),
            'outlet' => $episode->url(),
        ];
    }

    public function getPodloveEpisodeJson($episode)
    {
        $feedUtils = new Feed();
        $feed = $this->getFeedOfEpisode($episode);
        $audio = $feedUtils->getAudioFile($episode);
        $enclosure = $feedUtils->getAudioEnclosures($episode, $audio);
        $audioDuration = $feedUtils->getAudioDuration($audio);
        $chapters = $feedUtils->getChapters($episode, true, true);
        $contributors = $this->getPodloveContributors($episode->podcasterContributors(), $feed->podcasterPodloveRoles(), $feed->podcasterPodloveGroups());

        return [
            'version' => 5,
            'show' => [
                'title' => $feed->podcasterTitle()->value(),
                'subtitle' => $feed->podcasterSubtitle()->value(),
                'summary' => $feed->podcasterDescription()->value(),
                'poster' => $feed->podcasterCover()->toFile()->url(),
                'link' => $feed->podcasterLink()->value()
            ],
            'title' => $episode->podcasterTitle()->value(),
            'subtitle' => $episode->podcasterSubtitle()->value(),
            'summary' => $episode->podcasterDescription()->value(),
            'publicationDate' => $episode->date()->toDate('c'),
            'duration' => $audioDuration,
            'poster' => $feed->podcasterCover()->toFile()->url(),
            'link' => $episode->url(),
            'audio' => [
                [
                    'url' => $enclosure['url'],
                    'size' => $enclosure['length'],
                    'title' => "MP3 Audio",
                    'mimeType' => "audio/mpeg"
                ]
            ],
            'files' => [
                [
                    'url' => $enclosure['url'],
                    'size' => $enclosure['length'],
                    'title' => "MP3 Audio",
                    'mimeType' => "audio/mpeg"
                ]
            ],
            'chapters' => $chapters,
            'contributors' => $contributors,
        ];
        /*
            // TODO
            "transcripts" => [
                [
                    "start" => "00:00:00.005",
                    "start_ms" => 5,
                    "end" => "00:00:09.458",
                    "end_ms" => 9458,
                    "speaker" => "3",
                    "voice" => "Eric",
                    "text" => "Dann sage ich einfach mal: Hallo und willkommen zu Episode drei des Podlovers Podcasts. Heute das erste Mal mit Gast. Hallo Simon."
                ],
            ]
        ];
        */
    }

    public function getPodloveConfigJson($episode)
    {
        $feed = $this->getFeedOfEpisode($episode);
        $tokens = $this->getPodloveColors($feed);
        $fonts = $this->getPodloveFonts($feed);
        $clients = $this->getPodloveClients($feed);
        $sharing = $this->getPodloveSharing($feed, $episode);

        $config = [
            'version' => 5,
            'base' => 'player/',
            'activeTab' => $feed->podcasterPodloveActiveTab()->value(),
            'subscribe-button' =>
                !is_null($clients)
                    ? [ 'feed' => $feed->url() ]
                    : null,
        ];

        if (!is_null($tokens) || !is_null($fonts)) {
            $config['theme'] = [];
            if (!is_null($tokens)) {
                $config['theme']['tokens'] = $tokens;
            }
            if (!is_null($fonts)) {
                $config['theme']['fonts'] = $fonts;
            }
        }

        if (!is_null($clients)) {
            $config['subscribe-button']['clients'] = $clients;
        }

        if (!is_null($sharing)) {
            $config['share'] = $sharing;
        }

        // TODO Playlists

        return $config;
    }

    public function getAppleMetadata($endpoint)
    {
        $keyValueList = [];

        if (option('mauricerenck.podcaster.useApi', true)) {
            $apiCache = kirby()->cache('mauricerenck.podcaster');
            $jsonString  = $apiCache->get($endpoint);

            if ($jsonString === null) {
                $response = new Remote('https://api.podcaster-plugin.com/' . $endpoint);
                $apiCache->set($endpoint, $response->content(), 7 * 24 * 60);
            }
        } else {
            $jsonString = file_get_contents(__DIR__ . '/../res/' . $endpoint . '.json');
        }

        $json = json_decode($jsonString, JSON_OBJECT_AS_ARRAY);

        foreach ($json as $key => $label) {
            $keyValueList[] = ['text' => $label, 'value' => $key];
        }

        if (is_null($keyValueList)) {
            return [];
        }

        return $keyValueList;
    }
}
