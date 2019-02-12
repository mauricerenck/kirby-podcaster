<?php

/**
 * Podcast Plugin
 * @author Maurice Renck <hello@maurice-renck.de>
 * @version 2.0.0
 */

namespace Plugin\Podcaster;

use c;
use field;
use yaml;

class PodcasterStatsFile {

    public function increaseDownloads($page, $trackingDate) {
        $downloadDate = $this->formatTrackingDate($trackingDate);
        $fieldData = $page->downloads()->yaml();

        if ($this->trackingDateExists($fieldData, $downloadDate)) {

            for ($i = 0; $i < count($fieldData); $i++) {
                if ($fieldData[$i]['timestamp'] == $downloadDate) {
                    $fieldData[$i]['downloaded']++;
                }
            }
            $fieldData = yaml::encode($fieldData);

            $page->update(array('downloads' => $fieldData));
        } else {
            $this->addTrackingData($page, $fieldData, $downloadDate);
        }
    }

    private function trackingDateExists($structure, $downloadDate) {
        foreach ($structure as $trackingEntry) {
            if ($trackingEntry['timestamp'] == $downloadDate) {
                return true;
            }
        }

        return false;
    }

    private function addTrackingData($page, $fieldData, $downloadDate) {
        $fieldData[] = array(
            'timestamp' => $downloadDate,
            'downloaded' => 1
        );

        $fieldData = yaml::encode($fieldData);
        $page->update(array('downloads' => $fieldData));
    }

    public function increaseFeedVisits($page, $trackingDate) {
        $downloadDate = $this->formatTrackingDate($trackingDate);
        $fieldData = $page->downloads()->yaml();

        if ($this->trackingDateExists($fieldData, $downloadDate)) {

            for ($i = 0; $i < count($fieldData); $i++) {
                if ($fieldData[$i]['timestamp'] == $downloadDate) {
                    $fieldData[$i]['downloaded']++;
                }
            }
            $fieldData = yaml::encode($fieldData);

            $page->update(array('downloads' => $fieldData));
        } else {
            $this->addTrackingData($page, $fieldData, $downloadDate);
        }
    }

    private function formatTrackingDate(int $timestamp): string {
        return date('Y-m', $timestamp);
    }
}
