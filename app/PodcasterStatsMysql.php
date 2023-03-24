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
        [$podcastSlug, $downloadDate, $uuid] = $this->getUserAgentsQueryData($feed, $trackingDate);

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

    public function getDownloadsGraphData($podcast, $year, $month): object|bool
    {
        $query = 'SELECT DAY(created) AS day, SUM(downloads) AS downloads FROM episodes WHERE podcast_slug = "' . $podcast . '" AND YEAR(created) = ' . $year . ' AND MONTH(created) = ' . $month . '  GROUP BY created';

        return $this->database->query($query);
    }

    public function getQuickReports($podcast, $year, $month): array|bool
    {
        $query = 'SELECT DAY(created) AS day, SUM(downloads) AS downloads FROM episodes WHERE podcast_slug = "' . $podcast . '" AND YEAR(created) = ' . $year . ' AND MONTH(created) = ' . $month . '  GROUP BY created';
        $byMonthAndYear = $this->database->query($query);

        $queryAll = 'SELECT SUM(downloads) AS downloads FROM episodes WHERE podcast_slug = "' . $podcast . '"';
        $overall = $this->database->query($queryAll);

        return [
            'detailed' => $byMonthAndYear,
            'overall' => $overall,
        ];
    }

    public function getEpisodeGraphData($podcast, $episode): object|bool
    {
        $query = 'SELECT created AS date, downloads FROM episodes WHERE podcast_slug = "' . $podcast . '" AND episode_slug = "' . $episode . '";';

        return $this->database->query($query);
    }

    public function getEpisodesGraphData($podcast): object|bool
    {
        $query = 'SELECT YEAR(created) as year, MONTH(created) as month, SUM(downloads) AS downloads FROM episodes WHERE podcast_slug = "' . $podcast . '" GROUP BY YEAR(created), MONTH(created) ORDER BY YEAR(created), MONTH(created)';

        return $this->database->query($query);
    }

    public function getFeedsGraphData($podcast): object|bool
    {
        $query = 'SELECT YEAR(created) as year, MONTH(created) as month, SUM(downloads) AS downloads FROM feeds WHERE podcast_slug = "' . $podcast . '" GROUP BY YEAR(created), MONTH(created) ORDER BY YEAR(created), MONTH(created)';

        return $this->database->query($query);
    }

    public function getTopEpisodesByMonth($podcast, $year, $month): object|bool
    {
        $query = 'SELECT episode_name AS title,episode_slug AS slug, SUM(downloads) AS downloads FROM episodes WHERE podcast_slug = "' . $podcast . '" AND YEAR(created) = ' . $year . ' AND MONTH(created) = ' . $month . '  GROUP BY episode_slug LIMIT 10';

        return $this->database->query($query);
    }

    public function getTopEpisodes($podcast): object|bool
    {
        $query = 'SELECT episode_name AS title,episode_slug AS slug, SUM(downloads) AS downloads FROM episodes WHERE podcast_slug = "' . $podcast . '" GROUP BY episode_slug LIMIT 10';

        return $this->database->query($query);
    }

    public function getDevicesGraphData($podcast, $year, $month): object|bool
    {
        $query = 'SELECT device, SUM(downloads) AS downloads FROM devices WHERE podcast_slug = "' . $podcast . '" AND YEAR(created) = ' . $year . ' AND MONTH(created) = ' . $month . '  GROUP BY device';

        return $this->database->query($query);
    }

    public function getUserAgentGraphData($podcast, $year, $month): object|bool
    {
        $query = 'SELECT useragent, SUM(downloads) AS downloads FROM useragents WHERE podcast_slug = "' . $podcast . '" AND YEAR(created) = ' . $year . ' AND MONTH(created) = ' . $month . '  GROUP BY useragent';

        return $this->database->query($query);
    }

    public function getSystemGraphData($podcast, $year, $month): object|bool
    {
        $query = 'SELECT os, SUM(downloads) AS downloads FROM os WHERE podcast_slug = "' . $podcast . '" AND YEAR(created) = ' . $year . ' AND MONTH(created) = ' . $month . '  GROUP BY os';

        return $this->database->query($query);
    }

    public function getEstimatedSubscribers($podcast, $episodes): object|bool
    {
        $episodesQuery = [];
        foreach ($episodes as $episode => $date) {
            $episodesQuery[] =  '( episode_slug = "' . $episode . '" AND created <= "' . $date . '")';
        }

        $query = 'SELECT SUM(downloads) as total_downloads, episode_slug FROM episodes WHERE podcast_slug="' . $podcast . '" AND (' . implode(' OR ', $episodesQuery) . ') GROUP BY episode_slug;';

        return $this->database->query($query);
    }
}