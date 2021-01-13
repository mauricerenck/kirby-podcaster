# Kirby Podcaster

![GitHub release](https://img.shields.io/github/release/mauricerenck/kirby-podcaster.svg?maxAge=1800) ![License](https://img.shields.io/github/license/mashape/apistatus.svg) ![Kirby Version](https://img.shields.io/badge/Kirby-3%2B-black.svg)

**A Kirby 3 Podcast Plugin**

This plugin helps you running your own podcast with Kirby 3. It uses all the new panel features to make your life easier. You can run multiple podcasts and configure them in the panel. It creates Apple/Google/Name-Your-Favorite-Podcatcher Feeds and allows you to track downloads of your episodes and hits on your feeds. It comes with two audioplayers for your website

> **Attention!** As of version 2 of this plugin the file tracking method using markdown is **DEPRECATED**. See the update docs on how to update from v1 to v2

## Features

- ✅ Import wizard, move your existing podcast to kirby
- ✅ Panel blueprint section for episodes
- ✅ Panel blueprint for extended RSS feed (including all new iTunes specifications)
- ✅ Run multiple podcasts with just one Kirby installation
- ✅ Podcast Chapters
- ✅ Cover image per feed **and** episode
- ✅ Snippet for a simple HTML5-Player
- ✅ Snippet for the advanced Podlove Player
- ✅ Configure and style your website player within the panel
- ❌ Tracking of episodes/feeds using Kirby and the episode markdown (DEPRECATED in version 2!)
- ✅ Tracking of episodes/feeds using Kirby and SQlite
- ✅ Tracking of episodes/feeds using Kirby and MySQL
- ✅ Tracking of episodes/feeds using Matomo
- ✅ Tracking of episodes using PodTrac
- ✅ Statistics view in Panel
- ✅ Prefill fields from your ID3 data

## Quick install

Chose one of these options:

- `composer require mauricerenck/podcaster`
- unzip [master.zip](https://github.com/mauricerenck/kirby-podcaster/releases/latest) as folder `site/plugins/kirby-podcaster`
- `git submodule add https://github.com/mauricerenck/kirby-podcaster.git site/plugins/kirby-podcaster`

## Step by step guides

- **[Update from version 1](docs/update-v1-v2.md)**
- [Step by step install](docs/setup-clean.md)
- [Import existing Podcast](docs/setup-existing-podcast.md)
- [Starterkit tutorial](docs/kirby-podcaster-starterkit.md)

## Options

Use this options in `config.php` file to finetune the plugin. Write them like so: `'mauricerenck.podcaster.OPTION' = value`

| Option                   | Default    | Description                                                                                                  |
| ------------------------ | ---------- | ------------------------------------------------------------------------------------------------------------ |
| `statsInternal`          | `false`    | Enable internal download statistics                                                                          |
| `statsType`              | 'sqlite'   | Sets the method to save download stats using `statsInternal` - set to `sqlite` or `mysql`                    |
| `sqlitePath`             | -          | Set the path to the directory where the sqlite db for tracking should be stored                              |
| `statsSkipTableCreation` | false      | After creating/migrating the database set this option to `true` for more speed (see docs)                    |
| `statsHost`              | -          | Set your mysql hostname (for mysql tracking)                                                                 |
| `statsDatabase`          | -          | Set your mysql database name (for mysql tracking)                                                            |
| `statsUser`              | -          | Set your mysql database username (for mysql tracking)                                                        |
| `statsPassword`          | -          | Set your mysql database password (for mysql tracking)                                                        |
| `downloadTriggerPath`    | 'download' | Used to build the virtual url path for download tracking                                                     |
| `defaultFeed`            | 'feed'     | Used to find your podcasts rss feed (set this, if you changed the rss feed slug to soemthing else than feed) |
| `enableFeedStyling`      | `true`     | Set to `false` to disable XSL Styling of your podcast rss feed                                               |
| `matomoToken`            | -          | Set your matomo token for download tracking using matomo                                                     |
| `matomoBaseUrl`          | -          | Set your matomo base url for download tracking using matomo                                                  |
| `matomoBaseUrl`          | -          | Set your matomo base url for download tracking using matomo                                                  |
