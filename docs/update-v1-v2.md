# Update from version 1 to version 2

**Be aware! Some things changed in this major update. The most drastic change is that download tracking via the old file method is no longer supported! Use the new sqlite method instead.**

## Prequisits

**Backup!** Before doing anything, backup your data. Create a copy of your content folder and of your MySQL tables if you use MySQL for tracking downloads.

Before working on the update, it might be good idea to put your site into maintanance mode or at least make sure no one will access your rss feed, as this might cause trouble during the update process. Don't worry, the update should be done fairly quick. If you not sure, try to update on a local environmet or dev server before trying it on production.

## Update the plugin

Update the plugin using composer, git or download the newest release from github.

## Migrating data

When using MySQL for tracking you have to migrate existing data. This is done automatically when accessing the rss feed for the first time.
The Plugin looks for some new, needed tables and runs the migration if they aren't found.

After that migration your feed should be accessable as always. If so, open the panel, open the feed page and head to the statistics tab. You should see the updated statistics view with some in depth details. Open your feed and some mp3 urls of your episodes and check if data is updated in the stats view of the panel. You should be able to see useragents, systems and devices with some entries.

Because the plugin looks for the tables on every time it connects to the database you may want to disable this function after the tables are created. Go to your config file and add:

```
'mauricerenck.podcaster.statsSkipTableCreation' = true;
```

The plugin will now skip this step, future updates of the databases will still work.

## Cleaning up

Have a look at your database and your episodes and feed. Everything should be working by now.
