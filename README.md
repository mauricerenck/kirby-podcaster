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
* ✅ Tracking of downloads using Kirby and the episoden markdown
* ✅ Tracking of downloads using Kirby and MySQL
* ✅ Tracking of episodes/feeds using Matomo
* Snippet for Podlove Subscribe box


## Installation

* Clone repository to `/site/plugins/podcaster`
* In the panel create a new page for your podcast
* within your new podcast page, create an unlistet page for your rss-feed
* use the template `podcasterfeed` (currently it doesn't appear in the template list, I working on that)
* in the panel go to the rss-feed-page and edit all needed information

If you want to use the PodLove-Player and style it, you can try around here: https://docs.podlove.org/podlove-web-player/theme.html

## Configuration

### Internal Tracking
You can use the built in tracking by setting the following in your `config.php`
`'mauricerenck.podcaster.statsInternal' => true`

#### Episode
To track your episodes a special route has to be triggered. Kirby Podcaster does this by default. If you want, you can change this route by setting: 

`mauricerenck.podcaster.downloadTriggerPath`

Default is `download` 

#### Feed
If you want to track your rss-feed, you have to set the slug of your feed in the config.php. If your feed url is `https://podcast.tld/myfeed/` set:

```
'mauricerenck.podcaster.defaultFeed' => 'myfeed',
```

Default value is `feed`, so if you name your rss-feed-page `feed` everything is find and you don't have to do anything.

#### Tracking Mode
You can either use the file method, then your downloads will be directly stored in your episode markdown file. Note that this *can* lead to problems if there are a lot of simulatiously downoads. You may run better by using the mysql method. 

Or you can use MySQL:
```
    'mauricerenck.podcaster.statsType' => 'mysql',
    'mauricerenck.podcaster.statsHost' => 'HOSTNAME',
    'mauricerenck.podcaster.statsDatabase' => 'DATABASE',
    'mauricerenck.podcaster.statsUser' => 'USER',
    'mauricerenck.podcaster.statsPassword' => 'PASSWORD'
```
Before using the MySQL statistics please make sure to create the tables within your database. You can find the SQL import in the `res` directory of this repository called `podcasterStats.sql`

**NOTE**
Downloads can currenly be tracked, but there are no visual stats in the panel, yet.

### Matomo
You can enalbe download tracking via Matomo per feed. Go to your feed settings and click on the Tracking tab. Fill in all needed values.

To make sure Kirby Podcaster can access your Matomo API, you must set these to values in your config.php

```
'mauricerenck.podcaster.matomoToken' => 'my-secret-token',
'mauricerenck.podcaster.matomoBaseUrl' => 'https://my-matomo-url.tld/'
```

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

