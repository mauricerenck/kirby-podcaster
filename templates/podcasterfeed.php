<?php

namespace mauricerenck\Podcaster;

use Kirby\Toolkit\Xml;

$podcast = new Podcast();
$feed = new Feed();
// FIXME change response type to rss
//kirby()->response()->type('application/rss+xml');
kirby()->response()->type('text/xml');
?>
<?php snippet('podcaster-feed-header'); ?>
<rss version="2.0"
     xmlns:content="http://purl.org/rss/1.0/modules/content/"
     xmlns:wfw="http://wellformedweb.org/CommentAPI/"
     xmlns:rdf="http://www.w3.org/1999/02/22-rdf-syntax-ns#"
     xmlns:psc="http://podlove.org/simple-chapters"
     xmlns:dc="http://purl.org/dc/elements/1.1/"
     xmlns:atom="http://www.w3.org/2005/Atom"
     xmlns:sy="http://purl.org/rss/1.0/modules/syndication/"
     xmlns:slash="http://purl.org/rss/1.0/modules/slash/"
     xmlns:itunes="http://www.itunes.com/dtds/podcast-1.0.dtd"
     xmlns:googleplay="http://www.google.com/schemas/play-podcasts/1.0"
     xmlns:media="http://search.yahoo.com/mrss/"
>
    <channel>
        <atom:link href="<?=Xml::encode($page->atomLink());?>" rel="self" type="application/rss+xml"
                   title="<?php echo Xml::encode($page->podcasterTitle()); ?>"/>

        <lastBuildDate><?=$page->modified(DATE_RSS);?></lastBuildDate>
        <generator>Kirby Podcaster Plugin <?= $podcast->getPluginVersion(); ?></generator>

        <?=$feed->xmlTag('title', $page->podcasterTitle());?>
        <?=$feed->xmlTag('subtitle', $page->podcasterSubtitle());?>

        <?=$feed->xmlTag('link', $page->podcasterLink());?>

        <?=$feed->xmlTag('language', $page->podcasterLanguage());?>
        <?=$feed->xmlTag('docs', $page->podcasterLink());?>
        <?=$feed->xmlTag('copyright', $page->podcasterCopyright(), true);?>

        <?php snippet('podcaster-feed-description'); ?>

        <?=$feed->xmlTag('itunes:keywords', $page->Podcasterkeywords());?>

        <?php snippet('podcaster-feed-categories'); ?>
        <?php snippet('podcaster-feed-author'); ?>
        <?php snippet('podcaster-feed-owner'); ?>

        <?=$feed->xmlTag('itunes:type', $page->PodcasterType());?>

        <?php snippet(
            'podcaster-feed-cover',
            [
                'imageUrl' => $page->cover(),
                'title' => $page->podcasterTitle(),
                'link' => $page->podcasterLink(),
            ]
        ); ?>

        <?=$feed->xmlTag('itunes:explicit', $page->podcasterExplicit());?>
        <?=$feed->xmlTag('googleplay:explicit', $page->podcasterExplicit());?>

        <?=$feed->xmlTag('itunes:block', $page->podcasterBlock()->isTrue() ? 'Yes' : null);?>
        <?=$feed->xmlTag('googleplay:block', $page->podcasterBlock()->isTrue() ? 'Yes' : null);?>

        <?=$feed->xmlTag('itunes:complete', $page->podcasterComplete()->isTrue() ? 'Yes' : null);?>
        <?=$feed->xmlTag('itunes:new-feed-url', $page->podcasterNewFeedUrl());?>
    </channel>
</rss>