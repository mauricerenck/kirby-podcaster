import Episode from './components/Episode.vue'
import YearGraph from './components/YearGraph.vue'


panel.plugin('mauricerenck/podcaster', {
    sections: {
        'podcasterEpisodeStats': Episode,
        'podcasterYearlyGraph': YearGraph,
    }
});