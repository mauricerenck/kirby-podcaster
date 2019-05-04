<?php

namespace Plugin\Podcaster;

use c;
use database;
use str;
use DateTime;

class PodcasterStatsMySql {

    private $db;

    public function __construct() {
        $this->connect();
    }

     private function connect() {
        try {
            $this->db = new Database(array(
                'type' => 'mysql',
                'host' => option('mauricerenck.podcaster.statsHost'),
                'database' => option('mauricerenck.podcaster.statsDatabase'),
                'user' => option('mauricerenck.podcaster.statsUser'),
                'password' => option('mauricerenck.podcaster.statsPassword')
            ));

            return true;
        } catch (Exception $e) {
            echo 'Could not connect to Database: ', $e->getMessage(), "\n";
            return false;
        }
    }

    public function getEpisodeStats(string $uid, string $downloadDate) {
        $episodeDownloads = $this->db->table('podcaster_episodes');

        $results = $episodeDownloads->select(array('downloads'))
            ->where(['episode' => $uid, 'day' => $downloadDate])
            ->first();

        return $results;
    }

    public function getGroupedFeeds() {
        $episodeDownloads = $this->db->table('podcaster_feeds');
        return $episodeDownloads
            ->order('feed')
            ->group('feed')
            ->all();
    }

    public function getFeedStats(string $uid, string $downloadDate) {
        $episodeDownloads = $this->db->table('podcaster_feeds');

        $results = $episodeDownloads->select(array('downloads'))
            ->where(['feed' => $uid, 'day' => $downloadDate])
            ->first();

        return $results;
    }

    public function getFeedStatsByMonth(string $uid, string $timestamp) {

        $date = new DateTime($timestamp);
        $date->modify('first day of this month');
        $from = $date->format('Y-m-d');
        $date->modify('last day of this month');
        $to = $date->format('Y-m-d');

        $results = $this->db->query('SELECT SUM(downloads) AS downloaded FROM podcaster_feeds WHERE feed = "' . $uid . '" AND day BETWEEN "' . $from . '" and " ' . $to . ' "')->first();
        return $results;
    }

    public function getEpisodesStatsByMonth(string $podcast, string $timestamp) {

        $date = new DateTime($timestamp);
        $date->modify('first day of this month');
        $from = $date->format('Y-m-d');
        $date->modify('last day of this month');
        $to = $date->format('Y-m-d');

        $results = $this->db->query('SELECT *, SUM(downloads) AS downloaded FROM podcaster_episodes WHERE podcast = "' . $podcast . '" AND day BETWEEN "' . $from . '" and " ' . $to . ' " GROUP BY episode ORDER BY downloaded DESC');
        return $results;
    }

    public function getSingleEpisodesStatsByMonth(string $podcast, string $uid, string $timestamp) {

        $date = new DateTime($timestamp);
        $date->modify('first day of this month');
        $from = $date->format('Y-m-d');
        $date->modify('last day of this month');
        $to = $date->format('Y-m-d');

        $results = $this->db->query('SELECT *, SUM(downloads) AS downloaded FROM podcaster_episodes WHERE podcast = "' . $podcast . '" AND episode = "' . $uid . '" AND day BETWEEN "' . $from . '" and " ' . $to . ' " GROUP BY episode')->first();
        return $results;
    }

    public function getSingleEpisodesStats(string $podcast, string $uid) {
        $results = $this->db->query('SELECT *, SUM(downloads) AS downloaded FROM podcaster_episodes WHERE podcast = "' . $podcast . '" AND episode = "' . $uid . '" GROUP BY episode')->first();
        return $results;
    }

    public function increaseDownloads($episode, string $trackingDate) {
        $downloadDate = $this->formatTrackingDate($trackingDate);
        $episodeStats = $this->getEpisodeStats($episode->uid(), $downloadDate);

        $podcast = str::slug($episode->siblings()->find(option('mauricerenck.podcaster.defaultFeed', 'feed'))->podcasterTitle());

        if(!$episodeStats) {
            $this->setDownloads($podcast, $episode->uid(), $downloadDate);
            return true;
        }

        $this->updateDownloads($podcast, $episode->uid(), $downloadDate);
        return true;
    }

    private function setDownloads(string $podcast, string $uid, string $downloadDate) {
        $episode = $this->db->table('podcaster_episodes');
        $episode->insert([
            'podcast' => $podcast,
            'episode' => $uid,
            'day' => $downloadDate,
            'downloads' => 1
        ]);
    }

    private function updateDownloads(string $podcast, string $uid, string $downloadDate) {
        $this->db->execute('UPDATE podcaster_episodes SET downloads = downloads + 1 WHERE episode = "' . $uid . '" AND podcast = "' . $podcast . '" AND day = "' . $downloadDate . '"');
    }

    public function increaseFeedVisits($page, string $trackingDate) {
        $slug = str::slug($page->podcasterTitle());
        $downloadDate = $this->formatTrackingDate($trackingDate);
        $feedStats = $this->getFeedStats($slug, $downloadDate);

        if (!$feedStats) {
            $this->setFeedVisits($slug, $page->podcasterTitle(), $downloadDate);
            return true;
        }

        $this->updateFeedVisits($slug, $downloadDate);
        return true;
    }

    private function setFeedVisits(string $uid, string $title, string $downloadDate) {
        $episode = $this->db->table('podcaster_feeds');
        $episode->insert([
            'feed' => $uid,
            'podcast' => $title,
            'day' => $downloadDate,
            'downloads' => 1
        ]);
    }

    private function updateFeedVisits(string $uid, string $downloadDate) {
        $this->db->execute('UPDATE podcaster_feeds SET downloads = downloads + 1 WHERE feed = "' . $uid . '" AND day = "' . $downloadDate . '"');
    }

    private function formatTrackingDate($timestamp): string {
        return date('Y-m-d', $timestamp);
    }

}