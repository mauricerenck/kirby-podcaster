<?php

namespace mauricerenck\Podcaster;

$podcast = new Feed();
$feed = (isset($feed)) ? $feed : $podcast->getFeedOfEpisode($page);
$audio = $podcast->getAudioFile($page);

if (is_null($audio)) {
    return;
}

$enclosure = $podcast->getAudioEnclosures($page, $audio);
?>
<meta property="og:audio" content="<?php echo $enclosure['url']; ?>">