<?php

namespace mauricerenck\Podcaster;

use c;

class PodcasterStats
{
    private $statisticMode;
    private $stats;

    public function __construct()
    {
        $this->statisticMode = option('mauricerenck.podcaster.statsType', 'sqlite');

        if ($this->statisticMode == 'mysql') {
            $this->stats = new PodcasterStatsMySql();
        } else {
            $this->stats = new PodcasterStatsSqlite();
        }
    }

    public function increaseDownloads($podcast, $episode, $userAgentData): void
    {
        if (!$podcast) {
            return;
        }

        $trackingDate = time();

        if (option('mauricerenck.podcaster.statsInternal') === true) {
            $this->stats->increaseDownloads($podcast, $episode, $trackingDate);
            $this->stats->upsertUserAgents($podcast, $userAgentData, $trackingDate);
        }

        if ($podcast->podcasterMatomoEnabled()->isTrue()) {
            $matomoUtils = new PodcasterStatsMatomo($podcast->podcasterMatomoSiteId());
            $matomoUtils->trackEpisodeDownload($podcast, $episode);
        }

        if ($podcast->podTracEnabled()->isTrue()) {
            $podTrack = new PodcasterStatsPodTrac();
            $podTrack->increaseDownloads($podcast, $episode);
        }
    }

    public function increaseFeedVisits($feed)
    {
        $trackingDate = time();

        if (option('mauricerenck.podcaster.statsInternal') === true) {
            $this->stats->increaseFeedVisits($feed, $trackingDate);
        }

        if ($feed->podcasterMatomoFeedEnabled()->isTrue()) {
            $matomoUtils = new PodcasterStatsMatomo($feed->podcasterMatomoFeedSiteId());
            $matomoUtils->trackFeedDownload($feed);
        }
    }

    public function getTopDownloads($podcastId, int $limit)
    {
        return $this->stats->getTopDownloads($podcastId, $limit);
    }

    public function getEpisodeStatsByMonth(string $podcastId, int $year, int $month)
    {
        $stats = $this->getStatsClass();
        $timestamp = mktime(0, 0, 0, $month, 1, $year);

        $graphData = $stats->getEpisodeDownloadsByMonth($podcastId, $timestamp);
        $episodeData = $stats->getEpisodesStatsByMonth($podcastId, $timestamp);
        $userAgentData = $stats->getEpisodeUseragentsByMonth($podcastId, $timestamp);

        return ['graphData' => $graphData, 'episodeData' => $episodeData, 'userAgents' => $userAgentData];
    }

    public function getDownloadsOfYear(string $podcastId, string $years, string $type)
    {
        $statList = [];

        $yearList = explode('+', $years);

        foreach ($yearList as $year) {
            $timestamp = mktime(0, 0, 0, 1, 1, $year);
            $statList = array_merge($statList, $this->stats->getDownloadsOfYear($podcastId, $timestamp, $type));
        }

        return $statList;
    }

    private function getStatsClass()
    {
        if ($this->statisticMode == 'file') {
            return new PodcasterStatsFile();
        } elseif ($this->statisticMode == 'mysql') {
            return new PodcasterStatsMySql();
        } elseif ($this->statisticMode == 'sqlite') {
            return new PodcasterStatsSqlite();
        }

        return false;
    }
}
