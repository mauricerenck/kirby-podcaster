<template>
    <section class="k-modified-section podcaster podcaster-graph">
        <apexchart height="400" type="area" :options="options" :series="series"></apexchart>
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
                    id: 'episodes-all',
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
                    mode: this.themeMode,
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
        selectedPodcast() {
            this.getEpisodeGraphData()
        },
    },
    methods: {
        async getEpisodeGraphData() {
            const data = []
            this.$api
                .get(`podcaster/stats/graph/episodes/${this.selectedPodcast}`)
                .then((response) => {
                    if (!response || !response.downloads) return
                    response.downloads.forEach((entry) => {
                        data.push({ x: new Date(`${entry.year}-${entry.month}-02`).getTime(), y: entry.downloads })
                    })

                    this.series = [{ name: 'downloads', data: data }]
                })
                .catch((error) => {
                    console.log(error)
                })
        },
    },
    created() {
        this.getEpisodeGraphData()
    },
}
</script>
