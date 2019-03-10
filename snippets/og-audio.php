<?php
    namespace Plugin\Podcaster;

    require_once __DIR__ . '/../utils/PodcasterUtils.php';

    $podcasterUtils = new PodcasterUtils();
    $podcasterUtils->setCurrentEpisode($page);
    $audioFile = $podcasterUtils->getPodcastFile();
?>
<?php if($audioFile !== null) : ?>
<meta property="og:audio" content="<?php echo $page->url() . '/download/' . str_replace('.mp3', '', $audioFile->filename()); ?>">
<?php endif; ?>