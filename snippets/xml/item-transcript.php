<?php

namespace mauricerenck\Podcaster;

$feed = new Feed();

if (count($transcripts) === 0) {
    return null;
}

?>
<?php foreach ($transcripts as $transcript) : ?>
    <?= $feed->xmlTag('podcast:transcript ', null, false, $transcript); ?>
<?php endforeach; ?>
