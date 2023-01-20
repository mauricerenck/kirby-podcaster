<?php

namespace mauricerenck\Podcaster;

$feed = new Feed();
?>
<?php if ($page->podcasterOwner()->isEmpty()) {
    return;
} ?>
<?php if ($user = $page->podcasterOwner()->toUser()) : ?>
    <?=$feed->xmlTag('managingEditor', $user->email() . ' (' . $user->name() . ')');?>

    <itunes:owner>
        <?=$feed->xmlTag('itunes:name', $user->name());?>
        <?=$feed->xmlTag('itunes:email', $user->email());?>
    </itunes:owner>
<?php endif ?>
