<?php

namespace mauricerenck\Podcaster;
use MatomoTracker;

class PodcasterStatsMatomo
{
    private $matomo;

    public function __construct($feed)
    {
        if(!option('mauricerenck.podcaster.matomoBaseUrl', false) || !option('mauricerenck.podcaster.matomoToken', false)) {
            return;
        }

        $this->matomo = new MatomoTracker((int)$feed->podcasterMatomoSiteId(), option('mauricerenck.podcaster.matomoBaseUrl'));
        $this->matomo->setTokenAuth(option('mauricerenck.podcaster.matomoToken'));
        $this->matomo->disableSendImageResponse();
        $this->matomo->disableCookieSupport();
        $this->matomo->setIp($_SERVER['REMOTE_ADDR']);
    }

    public function trackEpisodeDownload($feed, $episode)
    {
        $this->matomo->setUrl($episode->url());

        if ($feed->podcasterMatomoGoalId()->isNotEmpty()) {
            $this->matomo->doTrackGoal($feed->podcasterMatomoGoalId(), 1);
        }

        if ($feed->podcasterMatomoEventName()->isNotEmpty()) {
            $this->matomo->doTrackEvent($feed->podcasterTitle(), $episode->title(), $feed->podcasterMatomoEventName());
        }

        if ($feed->podcasterMatomoAction()->isTrue()) {
            $this->matomo->doTrackAction($episode->url(), 'download');
        }
    }

    public function trackFeedDownload($feed)
    {
        $this->matomo->setUrl($feed->url());

        if ($feed->podcasterMatomoFeedPage()->isNotEmpty() && $feed->podcasterMatomoFeedPage()->isTrue()) {
            $this->matomo->doTrackPageView($feed->podcasterTitle());
        }

        if ($feed->podcasterMatomoFeedGoalId()->isNotEmpty()) {
            $this->matomo->doTrackGoal($feed->podcasterMatomoFeedGoalId(), 1);
        }

        if ($feed->podcasterMatomoFeedEventName()->isNotEmpty()) {
            $this->matomo->doTrackEvent($feed->podcasterTitle(), $feed->podcasterMatomoFeedEventName(), 1);
        }

        if ($feed->podcasterMatomoFeedAction()->isTrue()) {
            $this->matomo->doTrackAction($feed->url(), 'download');
        }
    }
}
