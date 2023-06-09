# Tips for your templates

To listen all the episodes of a podcast, you can use the according method of the Podcast class like so:

```php
<?php $podcast = new Podcast(); ?>
<?php foreach ($podcast->getEpisodes($page) as $episode) : ?>
    <li>
        <h2><?= $episode->podcasterTitle(); ?></h2>
        <?= $episode->podcasterDescription()->kirbytext(); ?>
        <a href="<?= $episode->url(); ?>">Listen</a>
    </li>
<?php endforeach; >
```

Have a look at the `podcasterfeed` template and its snippets to get some more tricks on how to handle episode data.