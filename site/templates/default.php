<html>
    <head>
    </head>
    <body>
        <div style="max-width: 500px; margin: 0 auto;"></div>

        <ul>
        <?php foreach ($site->index() as $item): ?>
            <li><a href="<?php echo $item->url(); ?>"><?php echo $item->title(); ?></a></li>
            <?php endforeach; ?>
        </ul>
    </body>
</html>