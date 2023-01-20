<?php

namespace mauricerenck\Podcaster;

$feed = new Feed();
?>
<?php if ($page->podcasterAuthor()->isEmpty()) {
    return;
} ?>
<?php if ($user = $page->podcasterAuthor()->toUser()) : ?>

    <?=$feed->xmlTag('itunes:author', $user->name());?>

    <?=$feed->xmlTag('googleplay:author', $user->name());?>
    <?=$feed->xmlTag('googleplay:email', $user->email());?>
<?php endif ?>
