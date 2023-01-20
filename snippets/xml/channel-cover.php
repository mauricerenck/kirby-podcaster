<?php
namespace mauricerenck\Podcaster;

use Kirby\Toolkit\Xml;

if(is_null($imageUrl)) {
    return null;
}

$feed = new Feed(); ?>
    <image>
        <?=$feed->xmlTag('url', $imageUrl);?>
        <?=$feed->xmlTag('title', $title);?>
        <?=$feed->xmlTag('link', $link);?>
    </image>

<?='<itunes:image href="' . Xml::encode($imageUrl) . '"/>';?>
<?='<googleplay:image href="' . Xml::encode($imageUrl) . '"/>'?>

