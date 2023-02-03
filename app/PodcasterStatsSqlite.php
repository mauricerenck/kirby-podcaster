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
            //$this->stats->upsertUserAgents($feed, $userAgentData, $trackingDate);
        }

        $this->trackEpisodeMatomo();
        $this->trackPodTrac();
    }

    public function upsertEpisode($feed, $episode, $trackingDate)
    {
        $uid = $episode->uid();
        $uuid = $episode->uuid();
        $downloadDate = $this->formatTrackingDate($trackingDate);
        $podcastSlug = $feed->podcastId();
        $uniqueHash = md5($episode->uid() . $podcastSlug . $downloadDate);

        $feedTitle = $feed->podcasterTitle()->text();
        $episodeTitle = $episode->title()->text();

        $fields = ['id', 'podcast_slug', 'uuid', 'podcast_name', 'episode_slug', 'episode_name', 'downloads', 'created'];
        $values = [$uniqueHash, $podcastSlug, $uuid, $feedTitle, $uid, $episodeTitle, 1, $downloadDate];

        $query = 'INSERT INTO episodes(' . implode(',', $fields) . ') VALUES("' . implode('","', $values) . '") ON CONFLICT(id) DO UPDATE SET downloads=downloads+1;';
        $this->database->query($query);
    }
}