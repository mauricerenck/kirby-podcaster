import Analytics from "./components/Analytics.vue";
import PodGraphEpisodesMonth from "./components/GraphDownloadsMonth.vue";
import PodGraphSingleEpisode from "./components/GraphSingleEpisode.vue";
import PodNavigation from "./components/Navigation.vue";
import PodStatWidget from "./components/StatWidget.vue";
import PodTopEpisodes from "./components/EpisodesTop.vue";
import PodEpisodesAutocomplete from "./components/EpisodesAutocomplete.vue";

panel.plugin("mauricerenck/podcaster", {
  components: {
    'k-podcaster-view': Analytics,
    'PodGraphEpisodesMonth': PodGraphEpisodesMonth,
    'PodGraphSingleEpisode': PodGraphSingleEpisode,
    'PodNavigation': PodNavigation,
    'PodStatWidget': PodStatWidget,
    'PodTopEpisodes': PodTopEpisodes,
    'PodEpisodesAutocomplete': PodEpisodesAutocomplete,
  }
});

