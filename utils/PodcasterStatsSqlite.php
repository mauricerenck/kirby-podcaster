<?php

namespace mauricerenck\Podcaster;

use c;
use database;
use str;
use DateTime;
use Db;
use f;
use str_replace;

class PodcasterStatsSqlite
{
    private $db;
    private $pluginPath;
    private $sqlitePath;

    public function __construct()
    {
        $this->connect();
        $this->migrate();
    }

    private function connect()
    {
        try {
            $this->pluginPath = option('mauricerenck.podcaster.sqlitePath');
            $this->pluginPath = str_replace('utils', '', __DIR__);

            $this->db = new Database([
                'type' => 'sqlite',
                'database' => $this->sqlitePath . 'podcaster.sqlite',
            ]);

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

        if (!$this->db->validateTable('settings')) {
            $migrationStructures = explode(';', f::read($this->pluginPath . 'migrations/sqlite_baseStructure.sql'));
            $migrationValues = explode(';', f::read($this->pluginPath . 'migrations/sqlite_baseValues.sql'));

            foreach ($migrationStructures as $query) {
                $this->db->execute(trim($query));
            }

            foreach ($migrationValues as $query) {
                $this->db->execute(trim($query));
            }

            $this->db->execute("INSERT INTO settings (podcaster_version) VALUES ('" . $package->version . "')");

            return true;
        }
        /*
                $settings = $this->db->table('settings');
                $pluginVersion = $settings->select(['podcaster_version'])->first();

                if ($pluginVersion->podcaster_version !== $package->version) {
                    $this->db->execute("UPDATE settings SET podcaster_version = '" . $package->version . "'");
                }
        */
    }

    public function getEpisodesStatsByMonth(string $podcastId, int $timestamp)
    {
        $date = new DateTime();
        $date->setTimestamp($timestamp);

        $date->modify('first day of this month');
        $from = $date->format('Y-m-d');

        $date->modify('last day of this month');
        $to = $date->format('Y-m-d');

        $results = $this->db->query('SELECT episode_name,log_date, SUM(downloads) AS downloaded FROM episodes WHERE podcast_slug = "' . $podcastId . '" AND log_date BETWEEN "' . $from . '" and "' . $to . '" GROUP BY episode_slug ORDER BY downloaded DESC');

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

        $results = $this->db->query('SELECT log_date, SUM(downloads) AS downloaded FROM episodes WHERE podcast_slug = "' . $podcastId . '" AND log_date BETWEEN "' . $from . '" and "' . $to . '" GROUP BY log_date ORDER BY downloaded DESC');

        $stats = new \stdClass();
        $stats->episodes = json_decode($results->toJson());

        return $stats;
    }

    public function getEpisodeUseragentsByMonth(string $podcastId, int $timestamp)
    {
        $date = new DateTime();
        $date->setTimestamp($timestamp);
        $from = $date->format('Y-m');

        $resultsDevices = $this->db->query('SELECT device , SUM(downloads) AS downloaded FROM devices WHERE podcast_slug = "' . $podcastId . '" AND log_date = "' . $from . '" GROUP BY device ORDER BY downloaded DESC');
        $resultsOs = $this->db->query('SELECT os , SUM(downloads) AS downloaded FROM os WHERE podcast_slug = "' . $podcastId . '" AND log_date = "' . $from . '" GROUP BY os ORDER BY downloaded DESC');
        $resultsUseragents = $this->db->query('SELECT useragent , SUM(downloads) AS downloaded FROM useragents WHERE podcast_slug = "' . $podcastId . '" AND log_date = "' . $from . '" GROUP BY useragent ORDER BY downloaded DESC');

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
        $results = $this->db->query('SELECT episode_name, SUM(downloads) AS downloaded FROM episodes WHERE podcast_slug = "' . $podcastId . '"  GROUP BY episode_slug ORDER BY downloaded DESC Limit ' . $limit);

        return json_decode($results->toJson());
    }

    public function getDownloadsOfYear(string $podcastId, int $timestamp, string $type)
    {
        $date = new DateTime();
        $date->setTimestamp($timestamp);
        $srcTable = ($type === 'episodes') ? 'episodes' : 'feeds';
        $year = $date->format('Y');
        $from = $year . '-01-01';
        $to = $year . '-12-31';

        $results = $this->db->query('SELECT log_date , SUM(downloads) AS downloaded FROM ' . $srcTable . ' WHERE podcast_slug = "' . $podcastId . '" AND log_date BETWEEN "' . $from . '" and "' . $to . '" GROUP BY strftime(\'%m\', log_date)');
        return json_decode($results->toJson());
    }

    public function getSingleEpisodesStatsByMonth(string $podcastId, string $uid, string $timestamp)
    {
        $date = new DateTime($timestamp);
        $date->modify('first day of this month');
        $from = $date->format('Y-m-d');
        $date->modify('last day of this month');
        $to = $date->format('Y-m-d');

        $results = $this->db->query('SELECT *, SUM(downloads) AS downloaded FROM episodes WHERE podcast_slug = "' . $podcastId . '" AND episode_slug = "' . $uid . '" AND day BETWEEN "' . $from . '" and " ' . $to . ' " GROUP BY episode_slug')->first();
        return $results;
    }

    public function increaseDownloads($podcast, $episode, int $trackingDate)
    {
        $podcasterUtils = new PodcasterUtils();
        $downloadDate = $this->formatTrackingDate($trackingDate);
        $podcastSlug = $podcast->podcastId()->or($podcast->slug());
        $uid = $episode->uid();

        $uniqueHash = md5($episode->uid() . $podcastSlug . $downloadDate);
        if (!$this->db->query('INSERT INTO episodes(id, podcast_slug, podcast_name, episode_slug, episode_name, log_date, downloads) VALUES("' . $uniqueHash . '", "' . $podcastSlug . '","' . $podcast->podcasterTitle() . '", "' . $uid . '","' . $episode->title() . '", "' . $downloadDate . '", 1)')) {
            $this->db->execute('UPDATE episodes SET downloads = downloads + 1 WHERE episode_slug = "' . $uid . '" AND podcast_slug = "' . $podcastSlug . '" AND log_date = "' . $downloadDate . '"');
        }

        return true;
    }

    public function upsertUserAgents($podcast, $userAgentData, string $downloadDate)
    {
        $podcastSlug = $podcast->podcastId()->or($podcast->slug());
        $yearMonth = date('Y-m', $downloadDate);

        $uniqueHash = md5($userAgentData['os'] . $podcastSlug . $yearMonth);
        if (!$this->db->query('INSERT INTO os(id, os, podcast_slug, log_date, downloads) VALUES("' . $uniqueHash . '", "' . $userAgentData['os'] . '","' . $podcastSlug . '", "' . $yearMonth . '", 1)')) {
            $this->db->execute('UPDATE os SET downloads = downloads + 1 WHERE podcast_slug = "' . $podcastSlug . '" AND log_date = "' . $yearMonth . '" AND os ="' . $userAgentData['os'] . '"');
        }

        $uniqueHash = md5($userAgentData['app'] . $podcastSlug . $yearMonth);
        if (!$this->db->query('INSERT INTO useragents(id, useragent,podcast_slug,log_date,downloads) VALUES("' . $uniqueHash . '", "' . $userAgentData['app'] . '","' . $podcastSlug . '", "' . $yearMonth . '", 1)')) {
            $this->db->execute('UPDATE useragents SET downloads = downloads + 1 WHERE podcast_slug = "' . $podcastSlug . '" AND log_date = "' . $yearMonth . '" AND useragent ="' . $userAgentData['app'] . '"');
        }

        $uniqueHash = md5($userAgentData['device'] . $podcastSlug . $yearMonth);
        if (!$this->db->query('INSERT INTO devices(id, device,podcast_slug,log_date,downloads) VALUES("' . $uniqueHash . '", "' . $userAgentData['device'] . '","' . $podcastSlug . '", "' . $yearMonth . '", 1)')) {
            $this->db->execute('UPDATE devices SET downloads = downloads + 1 WHERE podcast_slug = "' . $podcastSlug . '" AND log_date = "' . $yearMonth . '" AND device ="' . $userAgentData['device'] . '"');
        }
    }

    public function increaseFeedVisits($feed, string $trackingDate)
    {
        $feedSlug = $feed->podcastId()->or($feed->slug());
        $feedTitle = $feed->podcasterTitle();
        $downloadDate = $this->formatTrackingDate($trackingDate);

        $uniqueHash = md5($feedSlug . $downloadDate);
        if (!$this->db->query('INSERT INTO feeds(id, podcast_slug, podcast_name, log_date, downloads) VALUES("' . $uniqueHash . '", "' . $feedSlug . '","' . $feedTitle . '", "' . $downloadDate . '", 1)')) {
            $this->db->execute('UPDATE feeds SET downloads = downloads + 1 WHERE podcast_slug = "' . $feedSlug . '" AND log_date = "' . $downloadDate . '"');
        }

        return true;
    }

    private function formatTrackingDate(int $timestamp): string
    {
        return date('Y-m-d', $timestamp);
    }
}
