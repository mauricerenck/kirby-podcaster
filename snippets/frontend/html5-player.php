<?php

namespace mauricerenck\Podcaster;
$podcast = new Feed();

$episode = (isset($episode)) ? $episode : $page;
$feed = (isset($feed)) ? $feed : $podcast->getFeedOfEpisode($episode);

$audio = $podcast->getAudioFile($episode);
$enclosure = $podcast->getAudioEnclosures($episode, $audio);
?>
<div class="podcaster-html5-player">
	<audio controls>
		<source src="<?php echo $enclosure['url']; ?>" type="audio/mpeg">
		Your browser does not support the audio element.
	</audio>
</div>