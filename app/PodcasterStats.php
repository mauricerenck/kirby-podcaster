<?php

namespace mauricerenck\Podcaster;

class PodcasterStats
{
    public function stopIfIsBot($userAgentData)
    {
        return (option('mauricerenck.podcaster.doNotTrackBots', false) === true && $userAgentData['bot'] === true);
    }

    public function trackEpisodeMatomo()
    {
        // FIXME
        //if ($podcast->podcasterMatomoEnabled()->isTrue()) {
        //    $matomoUtils = new PodcasterStatsMatomo($podcast->podcasterMatomoSiteId());
        //    $matomoUtils->trackEpisodeDownload($podcast, $episode);
        //}
    }

    public function trackPodTrac()
    {
        // FIXME
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

    public function formatTrackingDate(int $timestamp): string
    {
        return date('Y-m-d', $timestamp);
    }
}
