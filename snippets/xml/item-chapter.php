<?php

namespace mauricerenck\Podcaster;

$feed = new Feed();

if(count($chapters) === 0) {
    return null;
}

?>
<psc:chapters version="1.2" xmlns:psc="http://podlove.org/simple-chapters">
    <?php foreach ($chapters as $chapter): ?>
        <?=$feed->xmlTag('psc:chapter', null, false, $chapter);?>
    <?php endforeach; ?>
</psc:chapters>