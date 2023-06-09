<?php

namespace mauricerenck\Podcaster;

class PodcasterStats implements PodcasterStatsInterfaceBase
{
    public function stopIfIsBot($userAgentData): bool
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

        $this->trackEpisodeMatomo($feed, $episode);
        $this->trackPodTrac($feed, $episode);
    }

    public function trackFeed($feed): void
    {
        if (!isset($feed)) {
            return;
        }

        if (option('mauricerenck.podcaster.statsInternal') === true) {
            $trackingDate = time();
            $this->upsertFeed($feed);
        }

        $this->trackFeedMatomo($feed);
    }

    public function upsertEpisode($feed, $episode, $trackingDate)
    {
        return;
    }


    public function upsertFeed($feed)
    {
        return;
    }


    public function upsertUserAgents($feed, array $userAgentData, int $trackingDate)
    {
        return;
    }

    public function trackEpisodeMatomo($feed, $episode): void
    {
        if ($feed->podcasterMatomoEnabled()->isTrue()) {
           $matomoUtils = new PodcasterStatsMatomo($feed);
           $matomoUtils->trackEpisodeDownload($feed, $episode);
        }
    }

    public function trackFeedMatomo($feed): void
    {
        if ($feed->podcasterMatomoEnabled()->isTrue()) {
           $matomoUtils = new PodcasterStatsMatomo($feed);
           $matomoUtils->trackFeedDownload($feed);
        }
    }

    public function trackPodTrac($feed, $episode): void
    {
        if ($feed->podTracEnabled()->isTrue()) {
            $podcast = new Podcast();
            $audioFile = $podcast->getAudioFile($episode);
            $episodeBaseUrl = str_replace(['https://', 'http://'], ['', ''], $episode->url());
            $podTracBaseUrl = rtrim($feed->podTracUrl(), '/');
            $podTracUrl = $podTracBaseUrl . '/' . $episodeBaseUrl . '/' . option('mauricerenck.podcaster.downloadTriggerPath', 'download') . '/' . $audioFile->filename();

            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $podTracUrl);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, false);
            curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
            curl_close($ch);
        }
    }

    public function getUserAgent(string $userAgent): array
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

    public function getFeedQueryData($feed): array
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

    public function getEpisodeQueryData($feed, $episode, $trackingDate): array
    {
        $podcast = new Podcast();
        $uid = $episode->uid();
        $uuid = $episode->uuid();
        $downloadDate = $this->formatTrackingDate($trackingDate);
        $podcastSlug = $feed->podcastId();
        $uniqueHash = md5($episode->uid() . $podcastSlug . $downloadDate);
        $audio = $podcast->getAudioFile($episode);
        $fileSize = $audio->size();

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
            'file_size',
            'created',
        ];
        $values = [$uniqueHash, $podcastSlug, $uuid, $feedTitle, $uid, $episodeTitle, 1, $fileSize, $downloadDate];

        return [$fields, $values];
    }

    public function getUserAgentsQueryData($feed, int $trackingDate): array
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

    // TODO
    // public function calculateCarbonEmissions($$fileSize, $episode) {
    //     $file_size = 5 * 1024 * 1024; // convert MB to bytes
    //     $electricity_used = $file_size / 1024 / 1024 / 10; // assume 10 MB per kWh
    //     $co2_emissions = $electricity_used * 0.6; // assume 0.6 kg CO2 per kWh
    //     echo "CO2 emissions for the download: " . $co2_emissions . " kg";
    // }

}
