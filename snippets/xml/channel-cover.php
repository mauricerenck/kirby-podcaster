<?php

namespace mauricerenck\Podcaster;

use Kirby\Toolkit\Xml;

if (is_null($imageUrl)) {
    return null;
}

$feed = new Feed();

?>

<image>
    <?=$feed->xmlTag('url', $imageUrl);?>
    <?=$feed->xmlTag('title', $title);?>
    <?=$feed->xmlTag('link', $link);?>
</image>

<?=$feed->xmlTag('itunes:image', null, false, ['href' => Xml::encode($imageUrl)]);?>
<?=$feed->xmlTag('googleplay:image', null, false, ['href' => Xml::encode($imageUrl)]);?>


