<?php

namespace mauricerenck\Podcaster;

$feed = new Feed();
?>

<?php if ($page->podcasterDescription()->isEmpty()) {
    return;
} ?>

<?=$feed->xmlTag('description', $page->podcasterDescription());?>
<?=$feed->xmlTag('itunes:summary', $page->podcasterDescription());?>
<?=$feed->xmlTag('googleplay:description', $page->podcasterDescription());?>