<template>
    <section class="k-modified-section podcaster">
        <apexchart height="400" type="area" :options="options" :series="series"></apexchart>
    </section>
</template>
<script>
import VueApexCharts from 'vue-apexcharts'

export default {
    components: { apexchart: VueApexCharts },
    props: {
        selectedPodcast: String,
        selectedEpisodes: [String],
    },
    data() {
        return {
            options: {
                chart: {
                    id: 'episode-details',
                },
                stroke: {
                    curve: 'smooth',
                    width: 2,
                },
                markers: {
                    size: 0,
                },
                dataLabels: {
                    enabled: false,
                },
                theme: {
                    palette: 'palette3',
                },
                xaxis: {
                    type: 'datetime',
                },
            },
            series: [],
        }
    },
    watch: {
        selectedEpisodes() {
            this.getEpisodeGraphData()
        },
        selectedPodcast() {
            this.getEpisodeGraphData()
        },
    },
    methods: {
        async getEpisodeGraphData() {
            const graphData = []
            this.selectedEpisodes.forEach(async (episode) => {
                const response = await this.$api.get(
                    `podcaster/stats/graph/episode/${this.selectedPodcast}/${episode}`
                )

                const data = []
                response.forEach((day) => {
                    data.push({ x: new Date(day.date).getTime(), y: day.downloads })
                })

                graphData.push({ name: episode, data: data })
            })

            this.series = graphData
        },
    },
    created() {
        this.getEpisodeGraphData()
    },
}
</script>
