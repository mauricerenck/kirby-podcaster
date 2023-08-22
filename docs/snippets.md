# Snippets

## web player 

To embed a player into your site you can use a simple snippet coming with the Podcaster plugin:

```php
<?php snippet('podcaster-player', ['page' => $page]); ?>
```

The `page` option is only needed, if you want to embed the player anywhere outside of an episode template. If so, hand in the episode page as shown above


## og audio meta tag

```php
<?php snippet('podcaster-ogaudio'); ?>
```

