<?php

namespace mauricerenck\Podcaster;

interface PodcasterStatsInterfaceBase
{
    public function stopIfIsBot(string $userAgentData): bool;
    public function trackEpisode($feed, $episode, $userAgent): void;
    public function trackEpisodeMatomo(): void;
    public function trackPodTrac(): void;
    public function getUserAgent(string $userAgentData): array;
    public function getFeedQueryData($feed): array;
    public function getEpisodeQueryData($feed, $episode, $trackingDate): array;
    public function getUserAgentsQueryData($feed, int $trackingDate): array;
    public function formatTrackingDate(int $timestamp): string;
}

interface PodcasterStatsInterface extends PodcasterStatsInterfaceBase
{
    public function trackFeed($feed): void;
    public function upsertEpisode($feed, $episode, $trackingDate): void;
    public function upsertUserAgents($feed, array $userAgentData, int $trackingDate): void;
    public function getDownloadsGraphData($podcast, $year, $month): object | bool;
    public function getQuickReports($podcast, $year, $month): array | bool;
    public function getEpisodeGraphData($podcast, $episode): object | bool;
    public function getEpisodesGraphData($podcast): object | bool;
    public function getFeedsGraphData($podcast): object | bool;
    public function getTopEpisodesByMonth($podcast, $year, $month): object | bool;
    public function getTopEpisodes($podcast): object|bool;
    public function getDevicesGraphData($podcast, $year, $month): object | bool;
    public function getUserAgentGraphData($podcast, $year, $month): object | bool;
    public function getSystemGraphData($podcast, $year, $month): object | bool;
    public function getEstimatedSubscribers($podcast, $episodes): object | bool;
}