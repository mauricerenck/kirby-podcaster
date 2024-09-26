<?php

namespace mauricerenck\Podcaster;

$feed = new Feed();
$useUuid = option('mauricerenck.podcaster.feed.uuid', false);
$audioFile = $feed->getAudioFile($episode);
$pubDate = $feed->getRssDate($episode->date()->modified()->toDate());

if(is_null($audioFile)) {
    return;
}
?>
<item>
    <?=$feed->xmlTag('pubDate', $pubDate);?>

    <?=$feed->xmlTag('title', $episode->podcasterTitle()->or($episode->title()));?>
    <?=$feed->xmlTag('itunes:title', $episode->podcasterTitle()->or($episode->title()));?>
    <?=$feed->xmlTag('itunes:subtitle', $episode->podcasterSubtitle());?>

    <?=$feed->xmlTag('link', $episode->url());?>
    <?=$feed->xmlTag('atom:link', $episode->url());?>
    <?=$feed->xmlTag('guid', $feed->getGuid($episode, $useUuid));?>

    <?=$feed->xmlTag('description', $episode->podcasterDescription()->kirbytext(), true);?>
    <?=$feed->xmlTag('content:encoded', $episode->podcasterDescription()->kirbytext(), true);?>
    <?=$feed->xmlTag('itunes:summary', $episode->podcasterDescription()->text());?>

    <?=$feed->xmlTag('itunes:season', $episode->podcasterSeason());?>
    <?=$feed->xmlTag('itunes:episode', $episode->podcasterEpisode());?>
    <?=$feed->xmlTag('itunes:episodeType', $episode->podcasterEpisodeType());?>

    <?=$feed->xmlTag('itunes:duration', $feed->getAudioDuration($audioFile));?>

    <?=$feed->xmlTag('itunes:explicit', $episode->podcasterExplicit());?>
    <?=$feed->xmlTag('googleplay:explicit', $episode->podcasterExplicit());?>

    <?=$feed->xmlTag('itunes:block', $episode->podcasterBlock()->isTrue() ? 'Yes' : false);?>
    <?=$feed->xmlTag('googleplay:block', $episode->podcasterBlock()->isTrue() ? 'Yes' : false);?>

    <?php snippet('podcaster-feed-item-cover', ['image' => $feed->getCoverFile($episode)]); ?>
    <?php snippet('podcaster-feed-item-chapter', ['chapters' => $feed->getChapters($episode)]); ?>
    <?php snippet('podcaster-feed-item-enclosure', ['episode' => $episode]); ?>
    <?php snippet('podcaster-feed-item-transcript', ['transcripts' => $feed->getTranscript($episode)]); ?>
</item>
