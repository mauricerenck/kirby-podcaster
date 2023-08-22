
# Fields

### RSS Feed

#### Feed base fields

| Field name          | Field type | Field values | Description                                                                                                                                             |
| ------------------- | ---------- | ------------ | ------------------------------------------------------------------------------------------------------------------------------------------------------- |
| podcastId           | slug       |              | Use something like my-podcast-id (must be unique)                                                                                                       |
| podcasterLink       | url        |              | Can be a link to your podcasts landing page                                                                                                             |
| podcasterAtomLink   | url        |              | If your want the target of your rss atom:link tag to point elsewhere, set the url here. This can be handy, if you want to use something like feedburner |
| podcasterSource     | pages      |              | Select the pages which will act as a parent of your episodes. Normally this is the page which is the parent of this feed, too.                          |
| podcasterBlock      | toggle     |              | Enabling this will prevent your Podcast from appearing on the Apple and other podcast directories.                                                      |
| podcasterExplicit   | toggle     |              | Enabling this will mark your podcast as explicit and may cause that it's not available for certain age groups.                                          |
| podcasterComplete   | toggle     |              | Enabling this will tell podcast directories that there won't be any new episodes.                                                                       |
| podcasterNewFeedUrl | url        |              | Leave empty if you do not want to change your feed url                                                                                                  |


#### Feed detail fields

| Field name           | Field type | Field values     | Description         |
| -------------------- | ---------- | ---------------- | ------------------- |
| podcasterCover       | files      |                  | Cover Image         |
| podcasterTitle       | text       |                  | Podcast Title       |
| podcasterSubtitle    | text       |                  | Podcast Subtitle    |
| podcasterDescription | text       |                  | Podcast Description |
| podcasterCopyright   | text       |                  | Podcast  Copyright  |
| podcasterKeywords    | tags       |                  | Apple Keywords      |
| podcasterCategories  | structure  | From API         | Apple Categories    |
| podcasterType        | radio      | episodic, serial | Podcast type        |
| podcasterLanguage    | select     | From API         | Podcast Language    |
| podcasterAuthor      | users      |                  | Author              |
| podcasterOwner       | users      |                  | Owner               |


#### Podlove Player

| Field name                    | Field type  | Field values                                                        | Description                                       |
| ----------------------------- | ----------- | ------------------------------------------------------------------- | ------------------------------------------------- |
| playerType                    | select      | html5, podlove                                                      | Player Type                                       |
| podcasterPodloveActiveTab     | select      | chapters, files, share                                              | Active Tab                                        |
| podcasterPodloveColors        | structure   |                                                                     | Colors                                            |
| podcasterPodloveFonts         | structure   |                                                                     | Fonts                                             |
| podcasterPodloveClients       | structure   | From API                                                            | Clients                                           |
| podcasterPodloveShareChannels | multiselect | facebook, twitter, whats-app, linkedin, pinterest, xing, mail, link | Share Playtime                                    |
| podcasterPodloveSharePlaytime | toggle      |                                                                     | Share Channels                                    |
| podcasterPodloveRoles         | structure   |                                                                     | Contributer Roles, used in the episode blueprint  |
| podcasterPodloveGroups        | structure   |                                                                     | Contributer Groups, used in the episode blueprint |


#### Podlove colors

| Field name | Field type | Field values                                                                       | Description |
| ---------- | ---------- | ---------------------------------------------------------------------------------- | ----------- |
| colorType  | select     | brand, brandDark, brandDarkest, brandLightest, shadeBase, shadeDark, contrast, alt | Color Type  |
| hex        | text       |                                                                                    | hex code    |

#### Podlove fonts

| Field name | Field type | Field values      | Description |
| ---------- | ---------- | ----------------- | ----------- |
| fontType   | select     | ci, regular, bold | Font Type   |
| name       | text       |                   | Font Name   |
| family     | text       |                   | Font Family |
| weight     | number     |                   | Font Weight |
| src        | tags       |                   | Source Path |


#### Contributer Roles

| Field name | Field type | Field values | Description |
| ---------- | ---------- | ------------ | ----------- |
| roleId     | number     |              | ID          |
| roleTitle  | text       |              | Role Title  |

#### Contributer Groups

| Field name | Field type | Field values | Description |
| ---------- | ---------- | ------------ | ----------- |
| groupId    | number     |              | ID          |
| groupTitle | text       |              | Group Title |

#### External Tracking

| Field name                    | Field type | Field values | Description                                                                                                               |
| ----------------------------- | ---------- | ------------ | ------------------------------------------------------------------------------------------------------------------------- |
| podcasterMatomoEnabled        | toggle     |              | Enable Matomo episode tracking                                                                                            |
| podcasterMatomoSiteId         | text       |              | Your Matomo Site Id                                                                                                       |
| podcasterMatomoTrackGoal      | toggle     |              | Enable if you want to track a certain goal for each download. Please be aware that you have to create this goal in Matomo |
| podcasterMatomoGoalId         | text       |              | Goal Id                                                                                                                   |
| podcasterMatomoTrackEvent     | toggle     |              | Enable if you want to track a certain event for each download                                                             |
| podcasterMatomoEventName      | text       |              | Event name                                                                                                                |
| podcasterMatomoAction         | toggle     |              | If you want to track the download action, enable this                                                                     |
| podcasterMatomoFeedEnabled    | toggle     |              | Enable Matomo rss-feed tracking                                                                                           |
| podcasterMatomoFeedSiteId     | text       |              | Your Matomo Site Id                                                                                                       |
| podcasterMatomoFeedTrackGoal  | toggle     |              | Enable if you want to track a certain goal for each download. Please be aware that you have to create this goal in Matomo |
| podcasterMatomoFeedPage       | toggle     |              | Will create a page view when feed is viewed                                                                               |
| podcasterMatomoFeedGoalId     | text       |              | Goal Id                                                                                                                   |
| podcasterMatomoFeedTrackEvent | toggle     |              | Enable if you want to track a certain event for each download                                                             |
| podcasterMatomoFeedEventName  | text       |              | Event name                                                                                                                |
| podcasterMatomoFeedAction     | toggle     |              | If you want to track the download action, enable this                                                                     |
| podTracEnabled                | toggle     |              | Enable PodTrac tracking                                                                                                   |


## Episode

| Field name            | Field type | Field values         | Description                     |
| --------------------- | ---------- | -------------------- | ------------------------------- |
| podcasterAudio        | files      |                      |                                 |
| podcasterCover        | files      |                      |                                 |
| podcasterSeason       | number     |                      |                                 |
| podcasterEpisode      | number     |                      |                                 |
| podcasterEpisodeType  | options    | full, trailer, bonus |                                 |
| podcasterExplicit     | toggle     | true, false          |                                 |
| podcasterBlock        | toggle     | true, false          |                                 |
| podcasterTitle        | text       |                      |                                 |
| podcasterSubtitle     | text       |                      |                                 |
| podcasterDescription  | text       |                      |                                 |
| podcasterChapters     | structure  |                      |                                 |
| podcasterSubtitle     | text       |                      |                                 |
| podcasterAuthor       | users      |                      | See table podcasterChapters     |
| podcasterContributors | structure  |                      | See table podcasterContributors |

| Field name                | Field type | Field values | Description |
| ------------------------- | ---------- | ------------ | ----------- |
| podcasterChapterTimestamp | text       |              | hh:mm:ss    |
| podcasterChapterTitle     | text       |              |             |
| podcasterChapterUrl       | url        |              |             |
| podcasterChapterImage     | files      |              |             |
[podcasterChapters]

| Field name        | Field type | values | Description |
| ----------------- | ---------- | ------ | ----------- |
| contributorId     | text       |        |             |
| contributorName   | text       |        |             |
| contributorAvatar | url        |        |             |
| contributorRole   | options    |        | Via Query   |
| contributorGroup  | options    |        | Via Query   |
[podcasterContributors]


  
