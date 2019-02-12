# Changes

title -> podcasterTitel
Itunesimage -> podcasterCover
Itunessubtitle -> podcasterSubtitle
Language -> podcasterLanguage
Ituneskeywords -> podcasterKeywords
Description -> podcasterDescription
Itunesblock -> podcasterBlock
Itunesexplicit -> podcasterExplicit
Itunesauthor -> podcasterAuthor
Itunesowner, Itunesemail -> podcasterOwner
iTunesType -> podcasterType
iTunesCategories -> podcasterCategories


```
<?php snippet('podcaster-player'); ?>
foreach($children as $episode) {
    snippet('podcaster-player', ['page' => $episode]);
}

```
