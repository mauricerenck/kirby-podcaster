<?php

namespace mauricerenck\Podcaster;

use Kirby\Database\Database;

class PodcasterStatsMysql extends PodcasterStats
{
    private ?Database $database;

    public function __construct()
    {
        $podcasterDb = new PodcasterDatabase();
        $this->database = $podcasterDb->connect('mysql');
    }

    public function trackFeed($feed)
    {
        [$fields, $values] = $this->getFeedQueryData($feed);

        $query = 'INSERT INTO feeds(' . implode(',', $fields) . ') VALUES("' . implode(
                '","',
                $values
            ) . '") ON DUPLICATE KEY UPDATE downloads = downloads+1;';

        $this->database->execute($query);
    }

    public function upsertEpisode($feed, $episode, $trackingDate)
    {
        [$fields, $values] = $this->getEpisodeQueryData($feed, $episode, $trackingDate);

        $query = 'INSERT INTO episodes(' . implode(',', $fields) . ') VALUES("' . implode(
                '","',
                $values
            ) . '") ON DUPLICATE KEY UPDATE downloads=downloads+1;';

        $this->database->execute($query);
    }

    public function upsertUserAgents($feed, array $userAgentData, int $trackingDate)
    {
        [$podcastSlug, $downloadDate, $uuid] = $this->getUserAgentsQueryData($feed,$trackingDate);

        $uniqueHash = md5($userAgentData['os'] . $podcastSlug . $downloadDate);
        $fields = ['id', 'os', 'podcast_slug', 'uuid', 'created', 'downloads'];
        $values = [$uniqueHash, $userAgentData['os'], $podcastSlug, $uuid, $downloadDate, 1];

        $query = 'INSERT INTO os(' . implode(',', $fields) . ') VALUES("' . implode(
                '","',
                $values
            ) . '") ON DUPLICATE KEY UPDATE downloads=downloads+1;';

        $this->database->execute($query);

        $uniqueHash = md5($userAgentData['app'] . $podcastSlug . $downloadDate);
        $fields = ['id', 'useragent', 'podcast_slug', 'uuid', 'created', 'downloads'];
        $values = [$uniqueHash, $userAgentData['app'], $podcastSlug, $uuid, $downloadDate, 1];

        $query = 'INSERT INTO useragents(' . implode(',', $fields) . ') VALUES("' . implode(
                '","',
                $values
            ) . '") ON DUPLICATE KEY UPDATE downloads=downloads+1;';
        $this->database->execute($query);

        $uniqueHash = md5($userAgentData['device'] . $podcastSlug . $downloadDate);
        $fields = ['id', 'device', 'podcast_slug', 'uuid', 'created', 'downloads'];
        $values = [$uniqueHash, $userAgentData['device'], $podcastSlug, $uuid, $downloadDate, 1];

        $query = 'INSERT INTO devices(' . implode(',', $fields) . ') VALUES("' . implode(
                '","',
                $values
            ) . '") ON DUPLICATE KEY UPDATE downloads=downloads+1;';
        $this->database->execute($query);
    }
}