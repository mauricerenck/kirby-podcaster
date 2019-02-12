<?php
    namespace Plugin\Podcast;

    require_once __DIR__ . '/../utils/PodcasterUtils.php';

    $podcasterUtils = new PodcasterUtils($podcast);
    $podcasterUtils->setCurrentEpisode($page);

    $cover = false;
    if($page->podcasterCover()->isNotEmpty()) {
        $cover = $page->podcasterCover()->toFile()->resize(200)->url();
    } else if($podcast->podcasterCover()->isNotEmpty()) {
        $cover = $podcast->podcasterCover()->toFile()->resize(200)->url();
    }

    $audioFile = $podcasterUtils->getPodcastFile();
?>
<div id="podlovePlayer"></div>
<script src="//cdn.podlove.org/web-player/embed.js"></script>

<script>
    podlovePlayer('#podlovePlayer', {
        <?php if($podcast->podcasterPodloveMainColor()->isNotEmpty() && $podcast->podcasterPodloveHighlighColor()->isNotEmpty()) : ?>
        theme: {
            main: '<?php echo str_replace('#', '', $podcast->podcasterPodloveMainColor()); ?>',
            highlight: '<?php echo str_replace('#', '', $podcast->podcasterPodloveHighlighColor()); ?>'
        },
        <?php endif; ?>
        tabs: {
            info: <?php echo ($podcast->podcasterPodloveTabsInfo()->isTrue()) ? 'true' : 'false'; ?>,
            share: <?php echo ($podcast->podcasterPodloveTabsShare()->isTrue()) ? 'true' : 'false'; ?>,
            chapters: <?php echo ($podcast->podcasterPodloveTabsChapters()->isTrue()) ? 'true' : 'false'; ?>,
            audio: <?php echo ($podcast->podcasterPodloveTabsAudio()->isTrue()) ? 'true' : 'false'; ?>,
            download: <?php echo ($podcast->podcasterPodloveTabsDownload()->isTrue()) ? 'true' : 'false'; ?>
        },
        title: '<?php echo $page->podcasterTitle()->or($page->title()); ?>',
        subtitle: '<?php echo $page->podcasterSubtitle(); ?>',
        summary: '<?php echo $page->podcasterDescription(); ?>',
        publicationDate: '<?php echo date('r', $page->date()); ?>',
        <?php if($cover !== false) : ?>
            poster: '<?php echo $page->podcasterCover()->toFile()->resize(200)->url(); ?>',
        <?php endif; ?>
        link: '<?php echo $page->url(); ?>',
        show: {
            title: '<?php echo $podcast->podcasterTitle(); ?>',
            subtitle: '<?php echo $podcast->podcasterSubtitle(); ?>',
            summary: '<?php echo $podcast->podcasterDescription(); ?>',
            <?php if($cover !== false) : ?>
            poster: '<?php echo $podcast->podcasterCover()->toFile()->url(); ?>',
            <?php endif; ?>
            link: '<?php echo $podcast->podcasterLink(); ?>'
        },
        duration: '<?php echo $podcasterUtils->getAudioDuration(); ?>',
		<?php if ($page->podcasterChapters()->isNotEmpty()) : ?>
			chapters: [
				<?php foreach ($page->podcasterChapters()->toStructure() as $chapter) : ?>
					{
                        start: '<?php echo $chapter->podcasterChapterTimestamp(); ?>',
                        title: '<?php echo $chapter->podcasterChapterTitle(); ?>',
                        <?php echo ($chapter->podcasterChapterUrl()->isNotEmpty()) ? "href: '" . $chapter->podcasterChapterUrl() . "'," : ''; ?>
                        <?php echo ($chapter->podcasterChapterImage()->isNotEmpty()) ? "image: '" . $chapter->podcasterChapterImage()->toFile()->url() . "'" : ''; ?>
                    },
				<?php endforeach; ?>
			],
        <?php endif; ?>
        <?php if ($page->podcasterContributors()->isNotEmpty()) : ?>
        <?php $contributors = $page->podcasterContributors()->toUsers(); ?>
        contributors: [
            <?php foreach ($contributors as $contributor) : ?>
            {
                id: '<?php echo $contributor->id(); ?>',
                <?php echo ($contributor->avatar() !== null) ? "avatar: '" . $contributor->avatar()->url() . "'," : ''; ?>
                name: '<?php echo $contributor->name(); ?>'
            },
            <?php endforeach; ?>
        ],
        <?php endif; ?>
        audio: [
            {
                url: '<?php echo $page->url() . '/download/' . str_replace('.mp3', '', $audioFile->filename()); ?>',
                mimeType: 'audio/mpeg',
                size: <?php echo $audioFile->size() ?>,
                title: '<?php echo $audioFile->title() ?>'
            }
		]
    });
</script>
