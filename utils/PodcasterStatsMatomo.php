<?php

namespace Plugin\Podcaster;

use MatomoTracker;

class PodcasterStatsMatomo
{
    private $matomo;
    private $episode;

    public function __construct(string $siteId)
    {
        $this->matomo = new MatomoTracker($siteId, option('mauricerenck.podcaster.matomoBaseUrl'));
        $this->matomo->setTokenAuth(option('mauricerenck.podcaster.matomoToken'));
        $this->matomo->disableSendImageResponse();
        $this->matomo->disableCookieSupport();
        $this->matomo->setIp($_SERVER['REMOTE_ADDR']);
    }

    public function trackEpisodeDownload($podcast, $episode)
    {
        $this->matomo->setUrl($episode->url());

        if ($podcast->podcasterMatomoGoalId()->isNotEmpty()) {
            $this->matomo->doTrackGoal($podcast->podcasterMatomoGoalId(), 1);
        }

        if ($podcast->podcasterMatomoEventName()->isNotEmpty()) {
            $this->matomo->doTrackEvent($podcast->podcasterTitle(), $episode->title(), $podcast->podcasterMatomoEventName());
        }

        if ($podcast->podcasterMatomoAction()->isTrue()) {
            $this->matomo->doTrackAction($episode->url(), 'download');
        }
    }

    public function trackFeedDownload($podcast)
    {
        $this->matomo->setUrl($podcast->url());

        if ($podcast->podcasterMatomoFeedPage()->isNotEmpty() && $podcast->podcasterMatomoFeedPage()->isTrue()) {
            $this->matomo->doTrackPageView($podcast->podcasterTitle());
        }

        if ($podcast->podcasterMatomoFeedGoalId()->isNotEmpty()) {
            $this->matomo->doTrackGoal($podcast->podcasterMatomoFeedGoalId(), 1);
        }

        if ($podcast->podcasterMatomoFeedEventName()->isNotEmpty()) {
            $this->matomo->doTrackEvent($podcast->podcasterTitle(), $podcast->podcasterMatomoFeedEventName(), 1);
        }

        if ($podcast->podcasterMatomoFeedAction()->isTrue()) {
            $this->matomo->doTrackAction($podcast->url(), 'download');
        }
    }
}
