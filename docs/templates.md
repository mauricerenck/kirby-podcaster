
# Templates
The plugin comes with one central template for the RSS feed. You have to create your own templates for the episode listing and episode detail page - or reuse an existing one.

Here are some tips for your templates:

## Listing template
If you not only want to provide an RSS feed but also a listing on your website, you can use the Podcast class of the plugin like this:

```php
<?php
use mauricerenck\Podcaster\Podcast;

$rssPage = page('my-podcast/feed');
$podcastHelper = new Podcast();

$episodeList = $podcastHelper->getEpisodes($rssPage);
?>
```

This will return a collection of (episode) pages. The `getEpisodes()` method takes care of the structure you use, it will always return a list of all episodes of a registered source pages. 

If you want to use the same template for your season or yearly listing, you have to filter the results, for example:

```php
<?php
(…)

$episodeList = $podcastHelper->getEpisodes($rssPage);
$episodes = $episodeList->filterBy('podcasterSeason', param('season'));
?>
```

## Episode template
If you already have a page templates for posts for your blog, you could reuse that template. It should basically be a details page template. Add the information you want, [here are all the available fields][1].

To add an audio player to your template, you can use the player snippet:

```php
<?php snippet('podcaster-player'); ?>
```


[1]:	fields.md