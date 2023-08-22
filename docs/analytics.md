
# Podcaster Analytics
To track downloads and other metrics, you have enable tracking and configure how to save data. A database is needed for this feature. The plugins support sqlite and mysql. Sqlite is recommended.

## Enable analytics
To enable analytics, open your `config.php` file in `site/config/`. If you do not have config.php yet, [create a new one][1]. Add these lines to your config:

```php
'mauricerenck.podcaster' => [
    'statsInternal' => true,
],
```

## Use SQLite
To use SQLite to store data, you have to set the storage type and the path to the database file:

```php
'mauricerenck.podcaster' => [
    'statsInternal' => true,
    'statsType' => 'sqlite',
    'sqlitePath' => 'content/',
],
```

The option `sqlitePath` should be a path to a folder where your data is not wiped accidentally and might be backed up. **Do not add a filename, only the path is required!**

After saving your settings the migration will automatically run and create or update all database tables. Check if everything is working:

1. Open the panel
2. Open the main menu
3. Select Podcasts

You should see the analytics page without any data in it. If you used the example above, there should be a new file named `podcaster.sqlite` in your `content` folder.

## Use MySQL
To use MySQL to store data, you have to set the storage type and credentials for the database:

```php
'mauricerenck.podcaster' => [
    'statsInternal' => true,        
    'statsType' => 'mysql',
    'statsHost' => 'my.host.tld',
    'statsDatabase' => 'podcaster-database',
    'statsUser' => 'podcaster-user',
    'statsPassword' => 'my-secret-password',
],
```

> [!NOTE]()
> You can write your credentials directly into the config.php but it might be a better idea to use environment variables to do so. There are some Kirby plugins which help you doing so! For example: [https://getkirby.com/plugins/bnomei/dotenv][3]

After saving your settings the migration will automatically run and create or update all database tables. Check if everything is working:

1. Open the panel
2. Open the main menu
3. Select Podcasts

You should see the analytics page without any data in it. Open your database in PhpMyAdmin or another tool you use. You should see the newly created tables.

## Check if tracking is working
You can now check if the tracking is working:

1. Open the panel
2. Go to your podcast
3. Open the feed
4. You should see the feed code
5. Look for `<item>` and in there for `<enclosure…>` there should be an url to an mp3 file
6. Copy the url
7. Open it in a new tab, it should start playing

This way we triggered a feed download and an episode download. Now:

1. Open the panel
2. Open the main menu
3. Click on Podcasts

Your should see some data in there.

[1]:	https://getkirby.com/docs/guide/configuration
[3]:	https://getkirby.com/plugins/bnomei/dotenv