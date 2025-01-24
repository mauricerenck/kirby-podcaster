<template>
    <section class="k-modified-section podcaster podcaster-graph">
        <apexchart height="300" type="bar" :options="options" :series="series"></apexchart>
    </section>
</template>
<script>
import VueApexCharts from 'vue-apexcharts'

export default {
    components: { apexchart: VueApexCharts },
    props: {
        selectedPodcast: String,
        themeMode: String,
    },
    data() {
        return {
            options: {
                chart: {
                    id: 'downloads-top-10',
                },
                theme: {
                    mode: this.themeMode,
                    palette: 'palette3',
                },
                plotOptions: {
                    bar: {
                        horizontal: true,
                    },
                },
            },
            series: [],
        }
    },
    watch: {
        selectedPodcast() {
            this.getEpisodeGraphData()
        },
    },
    methods: {
        getEpisodeGraphData() {
            this.$api.get(`podcaster/stats/top-episodes/${this.selectedPodcast}`).then((response) => {
                const data = response.map((episode) => {
                    return {
                        x: episode.title,
                        y: episode.downloads,
                    }
                })
                this.series = [{ name: 'Downloads', data }]
                console.log(this.series)
            })
        },
    },
    created() {
        this.getEpisodeGraphData()
    },
}
</script>
