# Migration

The update from version 2 is a huge one. A lot of things changed within the Kirby system in the last months. **So make sure to backup your files and your database before updating.** I also recommend to do this update on a test environment first.

## Update the plugin
The easiest way to update the plugin is to use composer. If you didn't install it with composer, downloads the latest version and replace the podcaster folder within `site/plugins`.

Podcaster 3 needs PHP 8.x and Kirby 3.8 or upwards.

## Running migrations
If you use the internal stats feature make sure to create a backup of your existing database BEFORE installing the plugin. Installing the plugin and opening the site or the panel will start the migration progress. So please be aware of that.

The database structure changed a lot. While migration the new structure will be applied and existing data will be migrated if needed. If your database has a lot of entries, this can take some time. I would recommend not doing this on a live system, but run the migration locally and:

1. Use a local copy of your database to perform the migration. Afterwards you can overwrite the production database with the locally migrated one
2. Connect to the remote database locally and run the migration on your local machine. Be aware this can lead to errors on production during the time your database has the new structure but your production plugin is not updated yet.

You can also run the migration on the production system, but you should not do this during times of high load. 

## Sanitize database
When migrating without the sanitize option you will see a lot of empty fields in your old tracking entries. This won't affect the functionality of the plugin, but you might see some missing labels or data in the analytics view.

There is a script which tries to convert this data. To do so enable this option:

`'mauricerenck.podcaster.sanitize' => true,`

I highly recommend not doing this when there are a lot of people active on your site as it may take quite some time and blocks the database. If you can, do with a local copy and update the production database later on.

**Make sure to disable the sanitize option after the migration!**

## Migrating content
I tried not to interfere with existing fields whenever I could.  But there are some changes to be aware of:

The field `podcasterExplizit` is now named `podcasterExplicit`

The episode mp3 and the cover file are now fields with the type `files` and not sections anymore. It's recommended to transfer your content (by selecting the files), but it's not necessary. 

Switching to the newest version of the PodLove player change its configuration completely, none of the old fields are present anymore. Please check [this docs](player.md) to learn how to set it up.

The episode tab blueprint has been changed to `tabs/podcaster/episode`. The old version `tabs/podcasterepisode` is still there for backwards compatibility, but if you can, change it.

## Feed
The feed template is built in way more modular way. It uses snippets and cleaned up internal functions and classes. If you customized the feed-template, make sure to adapt your changes to the new structure.

## Player snippets

If you use one of the player snippets directly, you can throw away the `feed` option.

