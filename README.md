# Kirby Podcaster

Kirby 3 Podcast Plugin
For first screens of the panel have a look here: https://maurice-renck.de/de/projects/kirby-podcaster

## Status

* ✅ new, slimmer ID3-Lib
* ✅ ID3 parsing after upload (not as in Version 2 during first pageview of the episode/feed)
* ✅ Panel blueprint section for episodes (so you can extend your article blueprint)
* ✅ Panel blueprint for extended RSS feed (including all new iTunes specifications)
* ✅ Completely rewritten RSS feed generator
* ✅ Most settings now available per feed and in the panel not globally in your config.php
* ✅ Run multiple podcasts with just one Kirby installation
* ✅ Updated chapter syntax, now including image
* ✅ Cover image per feed **and** episode
* ✅ Author- and owner Settings per feed **and** episode
* ✅ Upload as many mp3s as you want, select the one for your episode
* ✅ Source selection, your feed can now be on another location than your episodes
* ✅ Optimized iTunes Category Handling. Just select the categories from a dropdown. Categorylist updated via GitHub
* ✅ Snippet for a simple HTML5-Player
* ✅ Snippet for the advanced Podlove Player
* ✅ Configure and style your website player within the panel
* ✅ Routing for downloads/tracking
* Snippet for Podlove Subscribe box
* Tracking of downloads using Matomo
* Tracking of downloads using Kirby and the episoden markdown
* Tracking of downloads using Kirby and MySQL


## Installation

* Clone repository to `/site/plugins/podcaster`
* In the panel create a new page for your podcast
* within your new podcast page, create an unlistet page for your rss-feed
* use the template `podcasterfeed` (currently it doesn't appear in the template list, I working on that)
* in the panel go to the rss-feed-page and edit all needed information

If you want to use the PodLove-Player and style it, you can try around here: https://docs.podlove.org/podlove-web-player/theme.html

## Add Player to template

If you want an audio player appear on your episode page, just add the follow snippet to your template: `<?php snippet('podcaster-player'); ?>`

If your feed isn't a sibling of your episodes and you selected another source in your rss-settings, you can hand in the episode page and the feed page like so: `<?php snippet('podcaster-player', ['page' => $episodePageObject, 'podcast' => $rssFeedPageObject]); ?>`

This could be also handy, if you want to show the player in your podcast listing.

## Add episode section to your blueprint

If you're using the panel you may want to edit the episode specific information in your article blueprint. You can do so, by adding a new tab using the blueprint `tabs/podcasterepisode` this could look like this:

```
tabs:
    content:
        label: Your sections and columns
        icon: text
    podcast:
        extends: tabs/podcasterepisode
```

If you never used tabs before, have a look here: https://getkirby.com/docs/guide/blueprints/tabs

You can now edit your episode as needed by iTunes and other directories.