import Analytics from './components/Analytics.vue'
import PodGraphEpisodesMonth from './components/GraphDownloadsMonth.vue'
import PodGraphSingleEpisode from './components/GraphSingleEpisode.vue'
import PodNavigation from './components/Navigation.vue'
import PodStatWidget from './components/StatWidget.vue'
import PodSubscribers from './components/Subscribers.vue'
import PodEpisodesAutocomplete from './components/EpisodesAutocomplete.vue'
import PodGraphDevices from './components/GraphDevicesMonth.vue'
import PodGraphUserAgents from './components/GraphUserAgentsMonth.vue'
import PodGraphSystems from './components/GraphSystemMonth.vue'
import PodGraphEpisodes from './components/GraphEpisodes.vue'
import PodGraphFeeds from './components/GraphFeeds.vue'
import PodGraphTopEpisodes from './components/GraphTopEpisodes.vue'

panel.plugin('mauricerenck/podcaster', {
    components: {
        'k-podcaster-view': Analytics,
        PodGraphEpisodesMonth: PodGraphEpisodesMonth,
        PodGraphSingleEpisode: PodGraphSingleEpisode,
        PodNavigation: PodNavigation,
        PodStatWidget: PodStatWidget,
        PodEpisodesAutocomplete: PodEpisodesAutocomplete,
        PodGraphDevices: PodGraphDevices,
        PodGraphUserAgents: PodGraphUserAgents,
        PodGraphSystems: PodGraphSystems,
        PodSubscribers: PodSubscribers,
        PodGraphEpisodes: PodGraphEpisodes,
        PodGraphFeeds: PodGraphFeeds,
        PodGraphTopEpisodes: PodGraphTopEpisodes,
    },
})
