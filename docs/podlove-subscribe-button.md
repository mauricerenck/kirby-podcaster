# Adding a subscribe button

You can add subscribe button to your pages. Use the kirbytag `podloveSubscribe` to do so.

**Example**

```
(podloveSubscribe: myPodcast feed: my-podcast/feed label: Subscribe lang: en)
```

| Option             | Required | Description                                        |
| ------------------ | -------- | -------------------------------------------------- |
| `podloveSubscribe` | yes      | Use the podcastId you defined in the feed settings |
| `feed`             | yes      | The slug to your feed                              |
| `label`            | yes      | The label of the button                            |
| `lang`             | no       | Language code ie 'de' or 'en'                      |
| `itunesUrl`        | no       | The iTunes url of your podcast                     |
| `classname`        | no       | One or multiple css classes added to the button    |

## Using the tag in your templates

```
<?= kirbytags('(podloveSubscribe: myPodcast feed: my-podcast/feed label: Subscribe lang: en)'); ?>
```

Read more about this [here](https://getkirby.com/docs/reference/templates/helpers/kirbytags).
