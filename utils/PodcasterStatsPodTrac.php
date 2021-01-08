<?php

/**
 * Podcast Plugin
 * @author Maurice Renck <hello@maurice-renck.de>
 * @version 2.0.0
 */

namespace Plugin\Podcaster;

class PodcasterStatsPodTrac
{
    public function increaseDownloads($podcast, $episode)
    {
        $podcasterUtils = new PodcasterUtils();

        if ($podcast->podTracUrl()->isNotEmpty()) {
            $podcasterUtils = new PodcasterUtils();
            $podcasterUtils->setCurrentEpisode($episode);
            $audioFile = $podcasterUtils->getPodcastFile();

            $episodeBaseUrl = str_replace(['https://', 'http://'], ['', ''], $episode->url());
            $podTracBaseUrl = rtrim($podcast->podTracUrl(), '/');
            $podTracUrl = $podTracBaseUrl . '/' . $episodeBaseUrl . '/' . option('mauricerenck.podcaster.downloadTriggerPath', 'download') . '/' . $audioFile->filename();

            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $podTracUrl);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
            $data = curl_exec($ch);
            curl_close($ch);
        }
    }
}
