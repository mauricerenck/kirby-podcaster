<?php

namespace mauricerenck\Podcaster;

class PodcasterStats
{
    public function stopIfIsBot($userAgentData)
    {
        return (option('mauricerenck.podcaster.doNotTrackBots', false) === true && $userAgentData['bot'] === true);
    }

    public function trackEpisode($feed, $episode, $userAgent): void
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

    public function upsertEpisode($feed, $episode, $trackingDate)
    {
        return;
    }

    public function upsertUserAgents($feed, array $userAgentData, int $trackingDate)
    {
        return;
    }

    // FIXME

    public function trackEpisodeMatomo()
    {
        //if ($podcast->podcasterMatomoEnabled()->isTrue()) {
        //    $matomoUtils = new PodcasterStatsMatomo($podcast->podcasterMatomoSiteId());
        //    $matomoUtils->trackEpisodeDownload($podcast, $episode);
        //}
    }

    // FIXME
    public function trackPodTrac()
    {
        //if ($podcast->podTracEnabled()->isTrue()) {
        //    $podTrack = new PodcasterStatsPodTrac();
        //    $podTrack->increaseDownloads($podcast, $episode);
        //}
    }

    public function getUserAgent(string $userAgent)
    {
        $userApp = json_decode(file_get_contents(__DIR__ . '/../res/user-agents.json'), true);

        foreach ($userApp as $client) {
            foreach ($client['user_agents'] as $agent) {
                if (!isset($agent['bot']) || $agent['bot'] !== true) {
                    $app = (isset($client['app'])) ? $client['app'] : 'unknown';
                    $device = (isset($client['device'])) ? $client['device'] : 'unknown';
                    $os = (isset($client['os'])) ? $client['os'] : 'unknown';
                    $bot = (isset($client['bot'])) ? $client['bot'] : false;

                    // info using # as delimiter, because patterns contain slashes
                    if (preg_match('#' . $agent . '#', $userAgent, $tmp)) {
                        return [
                            'app' => $app,
                            'os' => $os,
                            'device' => $device,
                            'bot' => $bot,
                        ];
                    }
                }
            }
        }

        return [];
    }

    public function getFeedQueryData($feed)
    {
        $feedSlug = $feed->podcastId();
        $feedTitle = $feed->podcasterTitle()->escape();
        $uuid = $feed->uuid();
        $downloadDate = $this->formatTrackingDate(time());
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

        return [$fields, $values];
    }

    public function getEpisodeQueryData($feed, $episode, $trackingDate)
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

        return [$fields, $values];
    }

    public function getUserAgentsQueryData($feed, int $trackingDate)
    {
        $podcastSlug = $feed->podcastId();
        $downloadDate = $this->formatTrackingDate($trackingDate);
        $uuid = $feed->uuid();

        return [$podcastSlug, $downloadDate, $uuid];
    }

    public function formatTrackingDate(int $timestamp): string
    {
        return date('Y-m-d', $timestamp);
    }

    public function getDownloadsGraphData($podcast, $year, $month): object | bool {
        return false;
    }
}
