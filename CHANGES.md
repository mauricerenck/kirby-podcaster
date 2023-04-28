# Changes

`option('mauricerenck.podcaster.defaultFeed', 'feed')` is no more used, please rename your feed page accordingly TODO DESCRIPTION HOW TO DO SO

`mauricerenck.podcaster.feed.uuid` kann jetzt für GUID genutzt werden

Episode:
Das Feld `podcasterExplizit` heißt jetzt `podcasterExplicit`

Das Tab-Blueprint sollte jetzt mit 'tabs/podcaster/episode' benutzt werden. die alte Variante ist wegen Kompatibiliät aber noch drin: 'tabs/podcasterepisode'

Der Podlove player hat jetzt roles und groups, die können im feed konfiguriert werden. sind keine gesetzt, gibt es den standard "Team".

Der podlove player nimmt entweder die aktuelle $page als quelle oder man kann ihm ['episode' => $kirbyPage] reingeben

Podlove player config hat sich komplett geändert, folgende felder fallen weg
-   podcasterPodloveMainColor
-   podcasterPodloveHighlighColor
-   podcasterPodloveTabsInfo
-   podcasterPodloveTabsShare
-   podcasterPodloveTabsChapters
-   podcasterPodloveTabsAudio
-   podcasterPodloveTabsDownload