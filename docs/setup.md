# Setup

If you haven't already, use one of these three methods to install the plugin:

- composer (recommended): `composer require mauricerenck/podcaster`
- zip file: download the [latest release](https://github.com/mauricerenck/podcaster/releases) unzip it, copy it to `site/plugins/podcaster`

---

## Prerequisites
You’ll need a blueprint and an according template which allows you to list subpages.  Have a look [here](templates.md), to see how you can get a list of all episodes for your listing.
You’ll also need a blueprint for episodes. If you have a blog, you could use the blueprint for blogposts. We will extend it later on.

## Structure

The first step is to decide what structure your podcast should have. There are three recommended options on how to structure your content:

1. Seasonal structure: Every season of your podcast has its own folder. All episodes of the season are living inside of the season-folder
2. Yearly structure: Every year has its own folder. All episodes of the year are living inside of the year-folder
3. Flat structure: All episodes are living in a single folder

Version 1 or 2 are recommended to keep things clear.

## Panel Setup

Login to the panel, create a new page for your podcast, let's name it "*The Podcaster*". Create a new page, name it "The Podcaster". Choose a template/blueprint allowing you to list child pages.

### Create the feed

- Within the page create a subpage "Feed"
- Choose the Template `Podcaster Feed` 
- Fill in at least all the required fields in the `Show Details` Tab
- Switch to `RSS Settings` and enter at least all required fields
  - `Podcast Id` should be a unique id for your podcast, without spaces or fancy characters, our sample podcast will have the id `the-podcaster`
  - `Link` is the full URL of your podcast
  - Leave `Source pages` empty for now, as we don't have any episodes yet
  - Save the changes
- We leave the feed settings for now

### Create an episode

In order to create an episode, you need a blueprint/template for that. The podcaster plugin does not provide such a blueprint as it highly depends on your site setup and structure, but there is a *tab* you can use to extend an existing blueprint or to create a new one. **It's recommended to have a date field named `date` in your episode blueprint.**

To add this tab to your blueprint do the following:

	tabs:
	  podcast:
	    extends: tabs/podcasterepisode
	  yourtabs:
	    ...

If you already use tabs, simply add the podcaster episode tab. If you don't use tabs yet, please have a look at [the Kirby docs section](https://getkirby.com/docs/guide/blueprints/layout#tabs).

Depending of the structure you've chosen, you need to create the episode directly or create a subfolder first. I'll use the *seasonal* structure, so I'll have to create a new page for the season first. This new page will have the episodes as subpages. The structure will look like so:

- The Podcaster
	- feed
	- season-01
		  - episode-01
		  - episode-02
		  - ...
	- season-02
		  - episode-01
		  - ...

The season pages need a template listing all its children, so does the "The Podcaster" page ([here are some tips for your templates](templates.md)). You can use a simple page blueprint for that, which is capable to list an add new subpages.

Within your first season create your first episode. Select your episode template to do so. You should now have a new page and see a tab `Episode`.

There are some required field:

- The audio file
- Episode type

Everything else can be filled but must not. Of course you should enter as much information as possible.

**You can but should not upload a cover image here. Apple recommends to embed that cover into your mp3 ID3 data instead.**

#### ID3 data

When uploading an mp3 file, the plugin tries to read needed data like the duration of the episode. This data will be written to the audio’s metadata file and later used in the feed or for the player.

In addition you can enable the option `mauricerenck.podcaster.setId3Data` which will then try to read 
- the id3 title
- the id3 subtitle
- the id3 description 
- the id3 chapters

This data will then be written to the page. If you enable this feature a reload of the panel page may be required to display this information after the upload.

If ready, publish the episode page.

### Adding sources to the feed

You feed will still be empty as we did not specify any sources. Head over to the feed page. Go to the `RSS Settings` and add the `season-01`  page as a source. *Always add the parent folder of your episodes, not your episodes directly.*  If you have multiple season, add all of them.

When you open your the rss feed you should see the published episode. Publish your feed. You could now submit your feed to the podcast directories of your choice.

### Kirby UUID Feature
With the latest releases came the UUID feature, which gives every file and page a unique Id. If you want to use this feature in the feed (which might be a good idea) you can enable it by setting this in your `site/config/config.php`

```
'mauricerenck.podcaster.feed.uuid' => true,
```

You should **not** set it on existing podcasts, as it changes the GUID of your episodes and this can result in duplicates on platforms like Apple Podcasts.

## That's it

The base setup is done. You could now start, enabling [analytics](tracking.md) or setting up the [web player](player.md). 