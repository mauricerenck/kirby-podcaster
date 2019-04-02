# Kirby Podcaster

![GitHub release](https://img.shields.io/github/release/mauricerenck/kirby-podcaster.svg?maxAge=1800) ![License](https://img.shields.io/github/license/mashape/apistatus.svg) ![Kirby Version](https://img.shields.io/badge/Kirby-3%2B-black.svg)

**A Kirby 3 Podcast Plugin**

This plugin helps you running your own podcast with Kirby 3. It uses all the new panel-features to make your life easier. You can run multiple podcasts and configure them in the panel. All new iTunes specifications are included and chapters are supported. It creates iTunes/Google/Name-Your-Favorite-Podcatcher Feeds and allows you to track downloads of your episodes and hits on your feeds. It comes with two Audioplayers, a simple html5-audio-player and the advanced Podlove-Player supporting things like chapters, cover and much more. Or you can use your own player, if you wish.

## Features

* ✅ Panel blueprint section for episodes
* ✅ Panel blueprint for extended RSS feed (including all new iTunes specifications)
* ✅ Run multiple podcasts with just one Kirby installation
* ✅ Podcast Chapters
* ✅ Cover image per feed **and** episode
* ✅ Snippet for a simple HTML5-Player
* ✅ Snippet for the advanced Podlove Player
* ✅ Configure and style your website player within the panel
* ✅ Tracking of episodes/feeds using Kirby and the episode markdown
* ✅ Tracking of episodes/feeds using Kirby and MySQL
* ✅ Tracking of episodes/feeds using Matomo
* ✅ Tracking of episodes using PodTrac

### Planned 
* Statistics view in Panel
* Snippet for Podlove Subscribe box

## Installation

- `composer require mauricerenck/podcaster`
- unzip [master.zip](https://github.com/mauricerenck/kirby-podcaster/releases/latest) as folder `site/plugins/podcaster`
- `git submodule add https://github.com/mauricerenck/kirby-podcaster.git site/plugins/podcaster`

## Configuration

## Create RSS-Feed
Log into the panel and go to the folder containing your podcast episodes. Add a new unlisted page and name it `feed` using the template `podcasterfeed`. Please note that there is a problem, the template currently doesn't appear in the template list, so you have to add or change it by hand, naming the markdown-file `podcasterfeed`. The feed can then be edited in the panel.

Fill in all needed information.

If you want you can give your feed another name, you then need to change the download-trigger, see below in the tracking-settings.

## Add episode tab to your blueprint

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


### Tracking

Kirby Podcaster allows you to track the downloads of your episode and your rss-feed. There are three main tracking options available:

1. Internal tracking
2. Matomo (Piwik)
3. PodTrac

You can combine those methods as you want.

**NOTE**
Downloads can currenly be tracked, but there are no visual stats in the panel, yet.

### Enable internal tracking

To use the internal tracking, activate it in your `config.php` file by setting:
```'mauricerenck.podcaster.statsInternal' => true```

### Tracking of episodes
Tracking the downloads of your episodes requires a route with a certain keyword which functions as a trigger. The default keyword is `download`. Within your feed the URLs will be automatically set to match this keyword. If you want to use another keyword, you can set it in your `config.php`

```'mauricerenck.podcaster.downloadTriggerPath' => 'trackdownload'```

### Tracking of feed downloads
If you want to track your rss-feed, you have to set the slug of your feed in the `config.php`. If your feed url is `https://podcast.tld/myfeed/` set:

```
'mauricerenck.podcaster.defaultFeed' => 'myfeed',
```

Default value is `feed`, so if you name your rss-feed-page `feed` everything is find and you don't have to do anything.

### Tracking Mode
You can either use the file method, then your downloads will be directly stored in your episode markdown file. Note that this *can* lead to problems if there are a lot of simulatiously downloads. You may run better by using the mysql method:

```
'mauricerenck.podcaster.statsType' => 'mysql',
'mauricerenck.podcaster.statsHost' => 'HOSTNAME',
'mauricerenck.podcaster.statsDatabase' => 'DATABASE',
'mauricerenck.podcaster.statsUser' => 'USER',
'mauricerenck.podcaster.statsPassword' => 'PASSWORD'
```

If you want to use the file method, set 
```'mauricerenck.podcaster.statsType' => 'file'```

**ATTENTION!**

Before using the MySQL statistics please make sure to create the tables within your database. You can find the SQL import in the `res` directory of this repository called `podcasterStats.sql`


## External tracking with matomo
To make sure Kirby Podcaster can access your Matomo API, you must set these to values in your config.php

```
'mauricerenck.podcaster.matomoToken' => 'my-secret-token',
'mauricerenck.podcaster.matomoBaseUrl' => 'https://my-matomo-url.tld/'
```

You can enable download tracking via Matomo for each feed. Go to your feed settings and click on the Tracking tab. Fill in all needed values.


## External tracking with podtrac
Just log into the panel, go to your feed and enable podtrac in the tracking-tab.


## Add Player to template

There are two players available.

1. The standard html5 player
2. The advanced podlove player (https://podlove.org/podlove-web-player/)

You can chose which player to use in the player-settings of your feed.

If you want an audio player appear on your episode page, just add the follow snippet to your template: `<?php snippet('podcaster-player'); ?>`

If your feed isn't a sibling of your episodes and you selected another source in your rss-settings, you can hand in the episode page and the feed page like so: `<?php snippet('podcaster-player', ['page' => $episodePageObject, 'podcast' => $rssFeedPageObject]); ?>`

This could be also handy, if you want to show the player in your podcast listing.

If you want to use the PodLove-Player and style it, you can try around here: https://docs.podlove.org/podlove-web-player/theme.html


## OpenGraph
You can add an opengraph entry for your episodes in your page header. This will automatically embed an audioplayer when for example linking in medium.com or facebook. You can just add the og-snippet anywhere between `<head></head>`

```<?php snippet('podcaster-ogaudio'); ?>```
