# Tracking

When you're running a Podcast, you would probably like to see how many people listen to your episodes. There are several ways to achieve that:

1. Use the Podcaster Analytics (recommended)
2. Use Matomo Tracking 
3. Use PodTrac

## Setting up Podcaster Analytics

In order to enable tracking of downloads (and other metrics). There are two ways to store tracking data:

1. SQLite (recommended)
2. MySQL

### Enable tracking
The first step is to enable the Podcaster Analytics feature. Open your Kirby config under `site/config/config.php` and add the following option:

`'mauricerenck.podcaster.statsInternal' => true`, 

Next step is to configure your database. 

#### SQLite

You have to set the `statType` to `sqlite` and you have to provide a path to where the database file should be stored. The path is relative to the root of your Kirby installation. In the example below we add it to the content folder:

```
    'mauricerenck.podcaster.statsInternal' => true,
    'mauricerenck.podcaster.statsType' => 'sqlite',
    'mauricerenck.podcaster.sqlitePath' => './content/',
```

#### MySQL

Create a MySQL database and store the credentials to your password manager. Then add the data to your Kirby config:

```
    'mauricerenck.podcaster.statsHost' => 'localhost',
    'mauricerenck.podcaster.statsDatabase' => 'podcaster',
    'mauricerenck.podcaster.statsUser' => 'username',
    'mauricerenck.podcaster.statsPassword' => 'myVerySecretPassword',
```

##### Hint
You might want to avoid storing credentials in your PHP files, especially if you use something like git to manage your code. In this case have a look at [this plugin](https://getkirby.com/plugins/bnomei/dotenv) which will allow you to work with `.env` files.

## Migrations
The Podcaster listens to some Kirby hooks and performs database  migrations in the background. To initialize your database, simply open the Kirby Panel. This will create all the tables in your database.

If you update the plugin in the future all needed database updates will be done in the same way. So it may be good idea to backup your database before updating the plugin. You never know…

## Viewing analytics in the Panel

If everything worked you should be able to see analytics in the panel. Open the Panel main menu and you should be able to see the `Podcasts`. Select the podcast you want to view, maybe try to download an episode and open the feed to see if it's working.

Have fun!