<?php
/**
 * Templates render the content of your pages.
 * They contain the markup together with some control structures like loops or if-statements.
 * The `$page` variable always refers to the currently active page.
 * To fetch the content from each field we call the field name as a method on the `$page` object, e.g. `$page->title()`.
 * This template lists all all the subpages of the `notes` page with their title date sorted by date and links to each subpage.
 * Snippets like the header, footer and intro contain markup used in multiple templates. They also help to keep templates clean.
 * More about templates: https://getkirby.com/docs/guide/templates/basics
 */
?>

<?php snippet('header') ?>

<main>
  <?php snippet('intro') ?>

  <div class="notes">
    <?php
      foreach ($page->children()->listed()->filter(function ($child) {
          return $child->date()->toDate() <= time();
      })
        ->filter(function ($child) {
            return $child->hasAudio();
        })
        ->sortBy('date', 'desc') as $episode):
    ?>
    <article class="note">
      <header class="note-header">
        <a href="<?= $episode->url() ?>">
          <h2><?= $episode->title() ?></h2>
          <time><?= $episode->date()->toDate('d F Y') ?></time>
          <?php echo $episode->intro()->kirbytext(); ?>
        </a>
      </header>
    </article>
    <?php endforeach ?>
  </div>

</main>

<?php snippet('footer') ?>
