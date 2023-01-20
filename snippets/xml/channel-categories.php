<?php

namespace mauricerenck\Podcaster;

use Kirby\Toolkit\Xml;
?>

    <?php if ($page->podcasterCategories()->isEmpty()) {
    return;
} ?>

<?php foreach ($page->podcasterCategories()->toStructure() as $category): ?>
    <?php $splittedCategory = explode('/', $category->podcasterMainCategory()); ?>

    <?php if (count($splittedCategory) > 1): ?>
        <itunes:category text="<?=Xml::encode($splittedCategory[0]);?>">
            <itunes:category text="<?=Xml::encode($splittedCategory[1]);?>"/>
        </itunes:category>
    <?php else: ?>
        <itunes:category text="<?=Xml::encode($splittedCategory[0]);?>"/>
    <?php endif; ?>
<?php endforeach; ?>