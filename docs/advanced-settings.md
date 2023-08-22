
# Advanced settings

## ID3 data

When uploading an mp3 file, the plugin tries to read needed data like the duration of the episode. This data will be written to the audio’s metadata file and later used in the feed or for the player.

In addition you can enable the option `mauricerenck.podcaster.setId3Data` which will then try to read 
- the id3 title
- the id3 subtitle
- the id3 description 
- the id3 chapters

This data will then be written to the page. If you enable this feature a reload of the panel page may be required to display this information after the upload.

If ready, publish the episode page.

## Kirby UUID Feature
With the latest releases came the UUID feature, which gives every file and page a unique Id. If you want to use this feature in the feed (which might be a good idea) you can enable it by setting this in your `site/config/config.php`

```php
'mauricerenck.podcaster.feed.uuid' => true,
```

You should **not** set it on existing podcasts, as it changes the GUID of your episodes and this can result in duplicates on platforms like Apple Podcasts.

## Disable Podcaster API
By default the plugin fetches data like languages and apple categories from the Podcaster API and caches them for some time. If you don’t want any external connections, you can disable the API by setting:

```php
'mauricerenck.podcaster.externalApi' => false,
```

This will fetch the data from the local file system which may cause the data to be not up to date.