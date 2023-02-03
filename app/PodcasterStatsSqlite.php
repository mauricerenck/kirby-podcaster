<?php

namespace mauricerenck\Podcaster;

class PodcasterStatsSqlite extends PodcasterStats
{
    private $database;

    public function __construct()
    {
        $podcasterDb = new PodcasterDatabase();
        $this->database = $podcasterDb->connect('sqlite');
    }

    public function trackEpisode($feed, $episode, $userAgent)
    {
        if (!isset($feed)) {
            return;
        }

        $userAgentData = $this->getUserAgent($userAgent);

        if ($this->stopIfIsBot($userAgentData)) {
            return;
        }

        if (option('mauricerenck.podcaster.statsInternal') === true) {
            $trackingDate = time();
            $this->upsertEpisode($feed, $episode, $trackingDate);
            $this->upsertUserAgents($feed, $userAgentData, $trackingDate);
        }

        $this->trackEpisodeMatomo();
        $this->trackPodTrac();
    }

    public function trackFeed($feed)
    {
        $trackingDate = time();
        $feedSlug = $feed->podcastId();
        $feedTitle = $feed->podcasterTitle()->escape();
        $uuid = $feed->uuid();
        $downloadDate = $this->formatTrackingDate($trackingDate);
        $uniqueHash = md5($feedSlug . $downloadDate);

        $fields = [
            'id',
            'podcast_slug',
            'uuid',
            'podcast_name',
            'created',
            'downloads',
        ];
        $values = [$uniqueHash, $feedSlug, $uuid, $feedTitle, $downloadDate, 1];
        $query = 'INSERT INTO feeds(' . implode(',', $fields) . ') VALUES("' . implode(
                '","',
                $values
            ) . '") ON CONFLICT(id) DO UPDATE SET downloads=downloads+1;';

        $this->database->query($query);
    }

    public function upsertEpisode($feed, $episode, $trackingDate)
    {
        $uid = $episode->uid();
        $uuid = $episode->uuid();
        $downloadDate = $this->formatTrackingDate($trackingDate);
        $podcastSlug = $feed->podcastId();
        $uniqueHash = md5($episode->uid() . $podcastSlug . $downloadDate);

        $feedTitle = $feed->podcasterTitle()->escape();
        $episodeTitle = $episode->title()->text();

        $fields = [
            'id',
            'podcast_slug',
            'uuid',
            'podcast_name',
            'episode_slug',
            'episode_name',
            'downloads',
            'created',
        ];
        $values = [$uniqueHash, $podcastSlug, $uuid, $feedTitle, $uid, $episodeTitle, 1, $downloadDate];

        $query = 'INSERT INTO episodes(' . implode(',', $fields) . ') VALUES("' . implode(
                '","',
                $values
            ) . '") ON CONFLICT(id) DO UPDATE SET downloads=downloads+1;';
        $this->database->query($query);
    }

    public function upsertUserAgents($feed, array $userAgentData, int $trackingDate)
    {
        $podcastSlug = $feed->podcastId();
        $downloadDate = $this->formatTrackingDate($trackingDate);
        $uuid = $feed->uuid();

        $uniqueHash = md5($userAgentData['os'] . $podcastSlug . $downloadDate);
        $fields = ['id', 'os', 'podcast_slug', 'uuid', 'created', 'downloads'];
        $values = [$uniqueHash, $userAgentData['os'], $podcastSlug, $uuid, $downloadDate, 1];

        $query = 'INSERT INTO os(' . implode(',', $fields) . ') VALUES("' . implode(
                '","',
                $values
            ) . '")  ON CONFLICT(id) DO UPDATE SET downloads=downloads+1;;';
        $this->database->query($query);

        $uniqueHash = md5($userAgentData['app'] . $podcastSlug . $downloadDate);
        $fields = ['id', 'useragent', 'podcast_slug', 'uuid', 'created', 'downloads'];
        $values = [$uniqueHash, $userAgentData['app'], $podcastSlug, $uuid, $downloadDate, 1];

        $query = 'INSERT INTO useragents(' . implode(',', $fields) . ') VALUES("' . implode(
                '","',
                $values
            ) . '")  ON CONFLICT(id) DO UPDATE SET downloads=downloads+1;;';
        $this->database->query($query);

        $uniqueHash = md5($userAgentData['device'] . $podcastSlug . $downloadDate);
        $fields = ['id', 'device', 'podcast_slug', 'uuid', 'created', 'downloads'];
        $values = [$uniqueHash, $userAgentData['device'], $podcastSlug, $uuid, $downloadDate, 1];

        $query = 'INSERT INTO devices(' . implode(',', $fields) . ') VALUES("' . implode(
                '","',
                $values
            ) . '")  ON CONFLICT(id) DO UPDATE SET downloads=downloads+1;;';
        $this->database->query($query);
    }
}