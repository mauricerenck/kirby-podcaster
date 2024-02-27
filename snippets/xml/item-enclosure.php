<?php

namespace mauricerenck\Podcaster;

$feed = new Feed();

$audio = $feed->getAudioFile($episode);
$enclosure = $feed->getAudioEnclosures($episode, $audio);
$attr = [
    'url' => $enclosure['url'],
    'length' => $enclosure['length'],
    'type' => $enclosure['type'] ?? "audio/mpeg",
];
?>
<?= $feed->xmlTag('enclosure', null, false, $attr); ?>
