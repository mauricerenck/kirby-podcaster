<?php

namespace mauricerenck\Podcaster;

use \Kirby\Toolkit\Xml;

$rssUtils = new PodcasterUtils();
$rssUtils->setFeed($page);
?>
<?php echo '<?xml version="1.0" encoding="UTF-8"?>' . PHP_EOL; ?>
<?php
    if (option('mauricerenck.podcaster.enableFeedStyling', true)) {
        echo  '<?xml-stylesheet type="text/xsl" href="' . $page->url() . '/podcaster-feed-style' . '"?>' . PHP_EOL;
    }
?>
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
		<atom:link href="<?php echo Xml::encode($page->podcasterAtomLink()->or($page->url())); ?>" rel="self" type="application/rss+xml" title="<?php echo Xml::encode($page->podcasterTitle()); ?>" />
		<?php $rssUtils->printFieldValue('rssFeed', 'title', 'podcasterTitle'); ?>
		<?php $rssUtils->printFieldValue('rssFeed', 'link', 'podcasterLink'); ?>
		<lastBuildDate><?php echo date('r', $page->modified()); ?></lastBuildDate>
		<generator>Kirby Podcaster Plugin</generator>
		<?php $rssUtils->printFieldValue('rssFeed', 'language', 'podcasterLanguage'); ?>
		<?php $rssUtils->printFieldValue('rssFeed', 'docs', 'podcasterLink'); ?>

		<?php $rssUtils->printFieldValue('rssFeed', 'description', 'podcasterDescription'); ?>
		<?php $rssUtils->printFieldValue('rssFeed', 'copyright', 'podcasterCopyright', true); ?>

		<?php if ($user = $page->podcasterAuthor()->toUser()) : ?>
			<itunes:author><?php echo Xml::encode($user->name()); ?></itunes:author>
		<?php endif ?>
		
		<?php if ($user = $page->podcasterOwner()->toUser()) : ?>
		<managingEditor><?php echo Xml::encode($user->email()); ?> (<?php echo Xml::encode($user->name()); ?>)</managingEditor>
		<itunes:owner>
			<itunes:name><?php echo Xml::encode($user->name()); ?></itunes:name>
			<itunes:email><?php echo Xml::encode($user->email()); ?></itunes:email>
		</itunes:owner>
		<?php endif ?>

		<?php $rssUtils->getCoverImage(); ?>

		<?php $rssUtils->printFieldValue('rssFeed', 'itunes:summary', 'podcasterDescription'); ?>
		<?php $rssUtils->printFieldValue('rssFeed', 'itunes:subtitle', 'podcasterSubtitle'); ?>
		<?php $rssUtils->printFieldValue('rssFeed', 'itunes:keywords', 'podcasterKeywords'); ?>
		<?php $rssUtils->printFieldValue('rssFeed', 'itunes:type', 'podcasterType'); ?>
		<?php $rssUtils->printItunesCategories(); ?>
		
		<?php $rssUtils->printBoolValue('rssFeed', 'itunes:block', 'podcasterBlock'); ?>
		<?php $rssUtils->printBoolValue('rssFeed', 'itunes:explicit', 'podcasterExplicit'); ?>
		<?php $rssUtils->printBoolValue('rssFeed', 'itunes:complete', 'podcasterComplete'); ?>
		<?php $rssUtils->printFieldValue('rssFeed', 'itunes:new-feed-url', 'podcasterNewFeedUrl'); ?>

		<?php if ($user = $page->podcasterAuthor()->toUser()) : ?>
			<googleplay:author><?php echo Xml::encode($user->name()); ?></googleplay:author>
			<googleplay:email><?php echo Xml::encode($user->email()); ?></googleplay:email>
		<?php endif ?>
		<?php $rssUtils->printFieldValue('rssFeed', 'googleplay:description', 'podcasterDescription'); ?>
		<?php $rssUtils->printBoolValue('rssFeed', 'googleplay:explicit', 'podcasterExplicit'); ?>
		<?php $rssUtils->printBoolValue('rssFeed', 'googleplay:block', 'podcasterBlock'); ?>

	<?php foreach ($rssUtils->getEpisodes() as $episode) : ?>
		<?php $rssUtils->setCurrentEpisode($episode); ?>
		<item>
			<title><?php echo Xml::encode($episode->podcasterTitle()->or($episode->title())); ?></title>
			<link><?php echo $episode->url(); ?></link>
			<atom:link href="<?php echo $episode->url(); ?>"/>

			<?php if ($episode->podcasterCover()->isNotEmpty()) : ?>
			<image href="<?php echo $episode->podcasterCover()->toFile()->url(); ?>" />
			<itunes:image href="<?php echo $episode->podcasterCover()->toFile()->url(); ?>" />
			<googleplay:image href="<?php echo $episode->podcasterCover()->toFile()->url(); ?>" />
			<?php endif; ?>

			<guid isPermaLink="false"><?php echo $rssUtils->getGuid(); ?></guid>
			<pubDate><?php echo date('r', $episode->date()->toDate()); ?></pubDate>
			<?php $rssUtils->printFieldValue('episode', 'description', 'podcasterDescription', true); ?>

			<?php $rssUtils->printFieldValue('episode', 'itunes:title', 'podcasterTitle'); ?>
			<?php $rssUtils->printFieldValue('episode', 'itunes:subtitle', 'podcasterSubtitle'); ?>
			<?php $rssUtils->printFieldValue('episode', 'itunes:summary', 'podcasterDescription'); ?>
			<itunes:duration><?php echo $rssUtils->getAudioDuration($episode); ?></itunes:duration>
			<?php $rssUtils->printFieldValue('episode', 'itunes:season', 'podcasterSeason'); ?>
			<?php $rssUtils->printFieldValue('episode', 'itunes:episode', 'podcasterEpisode'); ?>
			<?php $rssUtils->printFieldValue('episode', 'itunes:episodeType', 'podcasterEpisodeType'); ?>
			<?php $rssUtils->printBoolValue('episode', 'itunes:explicit', 'podcasterExplizit'); ?>
			<?php $rssUtils->printBoolValue('episode', 'itunes:block', 'podcasterBlock'); ?>

			<?php $rssUtils->printBoolValue('episode', 'googleplay:block', 'podcasterBlock'); ?>

			<?php $rssUtils->printBoolValue('episode', 'googleplay:explicit', 'podcasterExplizit'); ?>

			<?php if ($user = $episode->podcasterAuthor()->toUser()) : ?>
				<itunes:author><?php echo Xml::encode($user->name()); ?></itunes:author>
			<?php elseif ($user = $page->podcasterAuthor()->toUser()) : ?>
				<itunes:author><?php echo Xml::encode($user->name()); ?></itunes:author>
			<?php endif ?>

			<?php $rssUtils->printFieldValue('episode', 'content:encoded', 'podcasterDescription', true); ?>

			<?php echo $rssUtils->getAudioEnclosures($episode); ?>
			<?php echo $rssUtils->getChapters($episode); ?>
		</item>
	<?php endforeach; ?>

	</channel>
</rss>