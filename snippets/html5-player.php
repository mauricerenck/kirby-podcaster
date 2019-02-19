<?php
    namespace Plugin\Podcaster;

    require_once __DIR__ . '/../utils/PodcasterUtils.php';

    $podcasterUtils = new PodcasterUtils();
    $podcasterUtils->setFeed($podcast);
    $podcasterUtils->setCurrentEpisode($page);
    $audioFile = $podcasterUtils->getPodcastFile();
?>
<div class="podcaster-html5-player">
	<audio controls>
		<source src="<?php echo $page->url() . '/download/' . str_replace('.mp3', '', $audioFile->filename()); ?>" type="audio/mpeg">
		Your browser does not support the audio element.
	</audio>
</div>