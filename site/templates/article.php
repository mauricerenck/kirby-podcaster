<html>
    <head>
    <style>
        .container {
            display: grid;
            grid-template-columns: 50% 50%;
            max-width: 1024px; margin: 0 auto;
        }
    </style>
    <?php echo snippet('podcaster-ogaudio'); ?>
    </head>
    <body>
        <div class="container">
            <div class="col"><?php snippet('podcaster-player', ['page' => $page]); ?></div>
            <div class="col"><?php snippet('podcaster-podlove-player', ['page' => $page]); ?></div>
            <div class="col"><?php snippet('podcaster-html5-player', ['page' => $page]) ?></div>
            <div>
                <?= $page->text()->kirbytext(); ?>
            </div>
        </div>
    </body>
</html>