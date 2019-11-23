<?php

namespace Plugin\Podcaster;

use str;
use addslashes;

require_once __DIR__ . '/../utils/PodcasterUtils.php';

    $podcasterUtils = new PodcasterUtils();
    $podcasterUtils->setFeed($podcast);
    $podcasterUtils->setCurrentEpisode($page);

    $cover = false;
    if ($page->podcasterCover()->isNotEmpty()) {
        $cover = $page->podcasterCover()->toFile()->resize(200)->url();
    } elseif ($podcast->podcasterCover()->isNotEmpty()) {
        $cover = $podcast->podcasterCover()->toFile()->resize(200)->url();
    }

    $audioFile = $podcasterUtils->getPodcastFile();
?>
<div id="podlovePlayerContainer"></div>
<script src="//cdn.podlove.org/web-player/embed.js"></script>

<script>
    podlovePlayer('#podlovePlayerContainer', {
        <?php if ($podcast->podcasterPodloveMainColor()->isNotEmpty() && $podcast->podcasterPodloveHighlighColor()->isNotEmpty()) : ?>
        theme: {
            main: '#<?php echo str_replace('#', '', $podcast->podcasterPodloveMainColor()); ?>',
            highlight: '#<?php echo str_replace('#', '', $podcast->podcasterPodloveHighlighColor()); ?>'
        },
        <?php endif; ?>
        tabs: {
            info: <?php echo ($podcast->podcasterPodloveTabsInfo()->isTrue()) ? 'true' : 'false'; ?>,
            share: <?php echo ($podcast->podcasterPodloveTabsShare()->isTrue()) ? 'true' : 'false'; ?>,
            chapters: <?php echo ($podcast->podcasterPodloveTabsChapters()->isTrue()) ? 'true' : 'false'; ?>,
            audio: <?php echo ($podcast->podcasterPodloveTabsAudio()->isTrue()) ? 'true' : 'false'; ?>,
            download: <?php echo ($podcast->podcasterPodloveTabsDownload()->isTrue()) ? 'true' : 'false'; ?>
        },
        title: '<?php echo addslashes(Str::replace(Str::unhtml($page->podcasterTitle()->or($page->title())), "\n", ' ')); ?>',
        subtitle: '<?php echo addslashes(Str::replace(Str::unhtml($page->podcasterSubtitle()), "\n", ' ')); ?>',
        summary: '<?php echo addslashes(Str::replace(Str::unhtml($page->podcasterDescription()), "\n", ' ')); ?>',
        publicationDate: '<?php echo date('r', $page->date()->toDate()); ?>',
        <?php if ($cover !== false) : ?>
            poster: '<?php echo $cover; ?>',
        <?php endif; ?>
        link: '<?php echo $page->url(); ?>',
        show: {
            title: '<?php echo addslashes(Str::replace(Str::unhtml(($podcast->podcasterTitle())), "\n", ' ')); ?>',
            subtitle: '<?php echo addslashes(Str::replace(Str::unhtml($podcast->podcasterSubtitle()), "\n", ' ')); ?>',
            summary: '<?php echo addslashes(Str::replace(Str::unhtml($podcast->podcasterDescription()), "\n", ' ')); ?>',
            <?php if ($cover !== false) : ?>
            poster: '<?php echo $cover; ?>',
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
