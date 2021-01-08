<?php

namespace Plugin\Podcaster;

use c;
use database;
use str;
use f;
use DateTime;

class PodcasterStatsMySql
{
    private $db;
    private $pluginPath;

    public function __construct()
    {
        $this->connect();
        $this->migrate();
    }

    private function connect()
    {
        try {
            $this->pluginPath = \str_replace('utils', '', __DIR__);

            $this->db = new Database([
                'type' => 'mysql',
                'host' => option('mauricerenck.podcaster.statsHost'),
                'database' => option('mauricerenck.podcaster.statsDatabase'),
                'user' => option('mauricerenck.podcaster.statsUser'),
                'password' => option('mauricerenck.podcaster.statsPassword')
            ]);

            $this->db->execute('SET sql_mode=(SELECT REPLACE(@@sql_mode, "ONLY_FULL_GROUP_BY", ""))');

            return true;
        } catch (Exception $e) {
            echo 'Could not connect to Database: ', $e->getMessage(), "\n";
            return false;
        }
    }

    private function migrate()
    {
        $composer = f::read(__DIR__ . '/../composer.json');
        $package = json_decode($composer);
        $migrated = false;

        $hasTables = $this->db->query('SELECT podcaster_id FROM podcaster_episodes LIMIT 1');
        $isVersion2 = $this->db->query('SELECT podcaster_version FROM podcaster_settings LIMIT 1');

        if (!$hasTables) {
            $migrationStructures = explode(';', f::read($this->pluginPath . 'migrations/mysql_baseStructure.sql'));

            foreach ($migrationStructures as $query) {
                $this->db->execute(trim($query));
            }

            $migrated = true;
        }

        // basic table exist from older version AND we are not on version 2
        // we need to run the migrations to from v1 to v2
        if ($hasTables !== false && !$isVersion2) {
            $migrationStructures = explode(';', f::read($this->pluginPath . 'migrations/mysql_2-0-0.sql'));

            foreach ($migrationStructures as $query) {
                $this->db->execute(trim($query));
            }

            $migrated = true;
        }

        $this->db->execute("INSERT INTO podcaster_settings (podcaster_version) VALUES ('" . $package->version . "')");
    }

    public function getEpisodesStatsByMonth(string $podcastId, int $timestamp)
    {
        $date = new DateTime();
        $date->setTimestamp($timestamp);

        $date->modify('first day of this month');
        $from = $date->format('Y-m-d');

        $date->modify('last day of this month');
        $to = $date->format('Y-m-d');

        $results = $this->db->query('SELECT episode_name,log_date, SUM(downloads) AS downloaded FROM podcaster_episodes WHERE podcast_slug = "' . $podcastId . '" AND log_date BETWEEN "' . $from . '" and "' . $to . '" GROUP BY episode_slug ORDER BY downloaded DESC');

        $stats = new \stdClass();
        $stats->episodes = json_decode($results->toJson());

        return $stats;
    }

    public function getEpisodeDownloadsByMonth(string $podcastId, int $timestamp)
    {
        $date = new DateTime();
        $date->setTimestamp($timestamp);

        $date->modify('first day of this month');
        $from = $date->format('Y-m-d');

        $date->modify('last day of this month');
        $to = $date->format('Y-m-d');

        $results = $this->db->query('SELECT log_date, SUM(downloads) AS downloaded FROM podcaster_episodes WHERE podcast_slug = "' . $podcastId . '" AND log_date BETWEEN "' . $from . '" and "' . $to . '" GROUP BY log_date ORDER BY downloaded DESC');

        $stats = new \stdClass();
        $stats->episodes = json_decode($results->toJson());

        return $stats;
    }

    public function getEpisodeUseragentsByMonth(string $podcastId, int $timestamp)
    {
        $date = new DateTime();
        $date->setTimestamp($timestamp);
        $from = $date->format('Y-m');

        $resultsDevices = $this->db->query('SELECT device, SUM(downloads) AS downloaded FROM podcaster_devices WHERE podcast_slug = "' . $podcastId . '" AND log_date = "' . $from . '" GROUP BY device ORDER BY downloaded DESC');
        $resultsOs = $this->db->query('SELECT os, SUM(downloads) AS downloaded FROM podcaster_os WHERE podcast_slug = "' . $podcastId . '" AND log_date = "' . $from . '" GROUP BY os ORDER BY downloaded DESC');
        $resultsUseragents = $this->db->query('SELECT useragent, SUM(downloads) AS downloaded FROM podcaster_useragents WHERE podcast_slug = "' . $podcastId . '" AND log_date = "' . $from . '" GROUP BY useragent ORDER BY downloaded DESC');

        $stats = new \stdClass();
        $stats = [
            'devices' => json_decode($resultsDevices->toJson()),
            'os' => json_decode($resultsOs->toJson()),
            'useragents' => json_decode($resultsUseragents->toJson()),
        ];

        return $stats;
    }

    public function getTopDownloads(string $podcastId, int $limit = 10)
    {
        $results = $this->db->query('SELECT episode_name, SUM(downloads) AS downloaded FROM podcaster_episodes WHERE podcast_slug = "' . $podcastId . '"  GROUP BY episode_slug ORDER BY downloaded DESC Limit ' . $limit);
        return json_decode($results->toJson());
    }

    public function getDownloadsOfYear(string $podcastId, int $timestamp, string $type)
    {
        $date = new DateTime();
        $date->setTimestamp($timestamp);
        $srcTable = ($type === 'episodes') ? 'podcaster_episodes' : 'podcaster_feeds';
        $year = $date->format('Y');
        $from = $year . '-01-01';
        $to = $year . '-12-31';

        $results = $this->db->query('SELECT log_date , SUM(downloads) AS downloaded FROM ' . $srcTable . ' WHERE podcast_slug = "' . $podcastId . '" AND log_date BETWEEN "' . $from . '" and "' . $to . '" GROUP BY MONTH(log_date)');
        return json_decode($results->toJson());
    }

    public function getFeedStatsByMonth(string $uid, string $timestamp)
    {
        $date = new DateTime();
        $date->setTimestamp($timestamp);

        $date->modify('first day of this month');
        $from = $date->format('Y-m-d');

        $date->modify('last day of this month');
        $to = $date->format('Y-m-d');

        $results = $this->db->query('SELECT SUM(downloads) AS downloaded FROM podcaster_feeds WHERE feed = "' . $uid . '" AND day BETWEEN "' . $from . '" and " ' . $to . ' "')->first();
        return $results;
    }

    public function getSingleEpisodesStatsByMonth(string $podcastId, string $uid, string $timestamp)
    {
        $date = new DateTime($timestamp);
        $date->modify('first day of this month');
        $from = $date->format('Y-m-d');
        $date->modify('last day of this month');
        $to = $date->format('Y-m-d');

        $results = $this->db->query('SELECT *, SUM(downloads) AS downloaded FROM podcaster_episodes WHERE podcast_slug = "' . $podcastId . '" AND episode_slug = "' . $uid . '" AND day BETWEEN "' . $from . '" and " ' . $to . ' " GROUP BY episode_slug')->first();
        return $results;
    }

    public function increaseDownloads($podcast, $episode, int $trackingDate)
    {
        $podcasterUtils = new PodcasterUtils();
        $downloadDate = $this->formatTrackingDate($trackingDate);
        $podcastSlug = $podcast->podcastId()->or($podcast->slug());
        $uid = $episode->uid();

        $uniqueHash = md5($episode->uid() . $podcastSlug . $downloadDate);
        if (!$this->db->query('INSERT INTO podcaster_episodes(podcaster_id, podcast_slug, podcast_name, episode_slug, episode_name, log_date, downloads) VALUES("' . $uniqueHash . '", "' . $podcastSlug . '","' . $podcast->podcasterTitle() . '", "' . $uid . '","' . $episode->title() . '", "' . $downloadDate . '", 1)')) {
            $this->db->execute('UPDATE podcaster_episodes SET downloads = downloads + 1 WHERE episode_slug = "' . $uid . '" AND podcast_slug = "' . $podcastSlug . '" AND log_date = "' . $downloadDate . '"');
        }

        return true;
    }

    public function upsertUserAgents($podcast, $userAgentData, string $downloadDate)
    {
        $podcastSlug = $podcast->podcastId()->or($podcast->slug());
        $yearMonth = date('Y-m', $downloadDate);

        $uniqueHash = md5($userAgentData['os'] . $podcastSlug . $yearMonth);
        if (!$this->db->query('INSERT INTO podcaster_os(podcaster_id, os, podcast_slug, log_date, downloads) VALUES("' . $uniqueHash . '", "' . $userAgentData['os'] . '","' . $podcastSlug . '", "' . $yearMonth . '", 1)')) {
            $this->db->execute('UPDATE podcaster_os SET downloads = downloads + 1 WHERE podcast_slug = "' . $podcastSlug . '" AND log_date = "' . $yearMonth . '" AND os ="' . $userAgentData['os'] . '"');
        }

        $uniqueHash = md5($userAgentData['app'] . $podcastSlug . $yearMonth);
        if (!$this->db->query('INSERT INTO podcaster_useragents(podcaster_id, useragent,podcast_slug,log_date,downloads) VALUES("' . $uniqueHash . '", "' . $userAgentData['app'] . '","' . $podcastSlug . '", "' . $yearMonth . '", 1)')) {
            $this->db->execute('UPDATE podcaster_useragents SET downloads = downloads + 1 WHERE podcast_slug = "' . $podcastSlug . '" AND log_date = "' . $yearMonth . '" AND useragent ="' . $userAgentData['app'] . '"');
        }

        $uniqueHash = md5($userAgentData['device'] . $podcastSlug . $yearMonth);
        if (!$this->db->query('INSERT INTO podcaster_devices(podcaster_id, device,podcast_slug,log_date,downloads) VALUES("' . $uniqueHash . '", "' . $userAgentData['device'] . '","' . $podcastSlug . '", "' . $yearMonth . '", 1)')) {
            $this->db->execute('UPDATE podcaster_devices SET downloads = downloads + 1 WHERE podcast_slug = "' . $podcastSlug . '" AND log_date = "' . $yearMonth . '" AND device ="' . $userAgentData['device'] . '"');
        }
    }

    public function increaseFeedVisits($feed, string $trackingDate)
    {
        $feedSlug = $feed->podcastId()->or($feed->slug());
        $feedTitle = $feed->podcasterTitle();
        $downloadDate = $this->formatTrackingDate($trackingDate);
        $uniqueHash = md5($feedSlug . $downloadDate);

        if (!$this->db->query('INSERT INTO podcaster_feeds(podcaster_id, podcast_slug, podcast_name, log_date, downloads) VALUES("' . $uniqueHash . '", "' . $feedSlug . '","' . $feedTitle . '", "' . $downloadDate . '", 1)')) {
            $this->db->execute('UPDATE podcaster_feeds SET downloads = downloads + 1 WHERE podcast_slug = "' . $feedSlug . '" AND log_date = "' . $downloadDate . '"');
        }

        return true;
    }

    private function formatTrackingDate(int $timestamp): string
    {
        return date('Y-m-d', $timestamp);
    }
}
