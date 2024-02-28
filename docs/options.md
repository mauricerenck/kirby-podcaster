# Options

To set an option, keep in mind that all of them have to be prefixed with `mauricerenck.podcaster`. For example:

```
'mauricerenck.podcaster.statsType' => 'sqlite',
```

or

```
'mauricerenck.podcaster' => [
    'statsInternal' => true,
    'statsType' => 'sqlite',
],
```


| Option              | default      | Description                                                                   |
| ------------------- | ------------ | ----------------------------------------------------------------------------- |
| defaultFeed         | `'feed'`     | This is used to track feet downloads                                          |
| downloadTriggerPath | `'download'` | This is used to track audio downloads                                         |
| feed.uuid           | `false`      | Use the new UUID feature for permalinks in the feed                           |
| statsInternal       | `false`      | Enable or disable Podcaster Analytics                                         |
| statsType           | `'sqlite'`   | Use `'sqlite'` or `'mysql` for storing stats data                             |
| sqlitePath          | `.`          | Path to the folder where the sqlite file should be created                    |
| statsHost           |              | MySQL hostname                                                                |
| statsDatabase       |              | MySQL database name                                                           |
| statsUser           |              | MySQL username                                                                |
| statsPassword       |              | MySQL password                                                                |
| doNotTrackBots      | `false`      | Will prevent bots to be counted in Podcaster Analytics                        |
| matomoBaseUrl       | `false`      | Your Matomo base url                                                          |
| matomoToken         |              | Your Matomo auth token                                                        |
| setId3Data          | `false`      | Write ID3 data like title or description to the episode page after mp3 upload |
| sanitize            | `false`      | Enable to sanitize data after migration from version 2                        |
| autoMigration       | `true`       | Disable this to not automatically migrate data (may help with low page speed) |
| useApi              | `true`       | Use the Podcaster API for Apple categories and other meta data                |
