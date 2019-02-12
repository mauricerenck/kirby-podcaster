<?php
namespace Plugin\Podcaster;
use c;

if(option('mauricerenck.podcaster.statsInternal') === true) {
	$stats = new PodcasterStats();
	$trackingDate = time();
	$stats->increaseFeedVisits($page, $trackingDate);
}

// TODO:
// Tracking via Matomo

$rssUtils = new PodcasterUtils($page);
?>
<?php echo '<?xml version="1.0" encoding="utf-8"?>' . PHP_EOL; ?>
<rss version="2.0" xmlns:atom="http://www.w3.org/2005/Atom" xmlns:cc="http://web.resource.org/cc/" xmlns:itunes="http://www.itunes.com/dtds/podcast-1.0.dtd" xmlns:media="http://search.yahoo.com/mrss/" xmlns:content="http://purl.org/rss/1.0/modules/content/" xmlns:rdf="http://www.w3.org/1999/02/22-rdf-syntax-ns#" xmlns:psc="http://podlove.org/simple-chapters">
	<channel>
		<atom:link href="<?php echo $page->podcasterAtomLink()->or($page->url()); ?>" rel="self" type="application/rss+xml" title="<?php echo $page->podcasterTitle() ?>"/>
		<?php $rssUtils->printFieldValue('rssFeed', 'title', 'podcasterTitle'); ?>
		<?php $rssUtils->printFieldValue('rssFeed', 'link', 'podcasterLink'); ?>
		<lastBuildDate><?php echo date('r', $page->modified()); ?></lastBuildDate>
		<generator>Kirby Podcaster Plugin</generator>
		<?php $rssUtils->printFieldValue('rssFeed', 'language', 'podcasterLanguage'); ?>
		<?php $rssUtils->printFieldValue('rssFeed', 'docs', 'podcasterLink'); ?>

		<?php $rssUtils->printFieldValue('rssFeed', 'description', 'podcasterDescription', true); ?>
		<?php $rssUtils->printFieldValue('rssFeed', 'copyright', 'podcasterCopyright', true); ?>

		<?php if ($user = $page->podcasterAuthor()->toUser()) : ?>
			<itunes:author><?php echo $user->name(); ?></itunes:author>
		<?php endif ?>
		
		<?php if ($user = $page->podcasterOwner()->toUser()) : ?>
		<managingEditor><?php echo $user->name(); ?></managingEditor>
		<itunes:owner>
			<itunes:name><?php echo $user->name(); ?></itunes:name>
			<itunes:email><?php echo $user->email(); ?></itunes:email>
		</itunes:owner>
		<?php endif ?>

		<?php $rssUtils->getCoverImage(); ?>

		<?php $rssUtils->printFieldValue('rssFeed', 'itunes:summary', 'podcasterDescription', true); ?>
		<?php $rssUtils->printFieldValue('rssFeed', 'itunes:subtitle', 'podcasterSubtitle', true); ?>
		<?php $rssUtils->printFieldValue('rssFeed', 'itunes:keywords', 'podcasterKeywords'); ?>
		<?php $rssUtils->printFieldValue('rssFeed', 'itunes:type', 'podcasterType'); ?>
		<?php $rssUtils->printItunesCategories(); ?>
		
		<?php $rssUtils->printBoolValue('rssFeed', 'itunes:block', 'podcasterBlock'); ?>
		<?php $rssUtils->printBoolValue('rssFeed', 'itunes:explicit', 'podcasterExplicit'); ?>
		<?php $rssUtils->printBoolValue('rssFeed', 'itunes:complete', 'podcasterComplete'); ?>
		<?php $rssUtils->printFieldValue('rssFeed', 'itunes:new-feed-url', 'podcasterNewFeedUrl'); ?>

	<?php foreach($rssUtils->getEpisodes() as $episode) : ?>
		<?php $rssUtils->setCurrentEpisode($episode); ?>
		<item>
			<title><?php echo $episode->podcasterTitle()->or($episode->title()); ?></title>
			<link><![CDATA[<?php echo $episode->url(); ?>]]></link>
			<atom:link href="<?php echo $episode->url(); ?>"/>

			<?php if($episode->podcasterCover()->isNotEmpty()) : ?>
			<itunes:image href="<?php echo $episode->podcasterCover()->toFile()->url(); ?>" />
			<?php endif; ?>

			<guid isPermaLink="false"><?php echo $rssUtils->getGuid(); ?></guid>
			<pubDate><?php echo date('r', $episode->date()); ?></pubDate>
			<?php $rssUtils->printFieldValue('episode', 'description', 'podcasterDescription', true); ?>

			<?php $rssUtils->printFieldValue('episode', 'itunes:title', 'podcasterTitle'); ?>
			<?php $rssUtils->printFieldValue('episode', 'itunes:subtitle', 'podcasterSubtitle'); ?>
			<?php $rssUtils->printFieldValue('episode', 'itunes:summary', 'podcasterDescription'); ?>
			<itunes:duration><?php echo $rssUtils->getAudioDuration($episode); ?></itunes:duration>
			<?php $rssUtils->printFieldValue('episode', 'itunes:season', 'podcasterSeason'); ?>
			<?php $rssUtils->printBoolValue('episode', 'itunes:explicit', 'podcasterExplizit'); ?>

			<?php if ($user = $episode->podcasterAuthor()->toUser()) : ?>
				<itunes:author><?php echo $user->name(); ?></itunes:author>
			<?php elseif ($user = $page->podcasterAuthor()->toUser()) : ?>
				<itunes:author><?php echo $user->name(); ?></itunes:author>
			<?php endif ?>

			<?php $rssUtils->printFieldValue('episode', 'content:encoded', 'podcasterDescription', true); ?>

			<?php echo $rssUtils->getAudioEnclosures($episode); ?>
			<?php echo $rssUtils->getChapters($episode); ?>
		</item>
	<?php endforeach; ?>
	</channel>
</rss>