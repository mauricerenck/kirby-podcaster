<?php

namespace mauricerenck\Podcaster;

use Kirby\Toolkit\Xml;

if (is_null($image)) {
    return null;
}

$feed = new Feed();

?>

<?=$feed->xmlTag('image', null, false, ['href' => Xml::encode($image->url())]);?>
<?=$feed->xmlTag('itunes:image', null, false, ['href' => Xml::encode($image->url())]);?>
<?=$feed->xmlTag('googleplay:image', null, false, ['href' => Xml::encode($image->url())]);?>
