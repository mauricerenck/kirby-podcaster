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
        selectedMonth: Number,
        selectedYear: Number,
        selectedPodcast: String,
        themeMode: String,
    },
    data() {
        return {
            options: {
                chart: {
                    id: 'downloads-month',
                },
                yaxis: {
                    labels: {
                        formatter: function (val) {
                            return val.toFixed(0)
                        },
                    },
                },
                theme: {
                    mode: this.themeMode,
                    palette: 'palette3',
                },
                xaxis: {
                    categories: [
                        1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15, 16, 17, 18, 19, 20, 21, 22, 23, 24, 25, 26,
                        27, 28, 29, 30, 31,
                    ],
                },
            },
            series: [],
        }
    },
    watch: {
        selectedMonth() {
            this.getEpisodeGraphData()
        },
        selectedYear() {
            this.getEpisodeGraphData()
        },
        selectedPodcast() {
            this.getEpisodeGraphData()
        },
    },
    methods: {
        getEpisodeGraphData() {
            this.$api
                .get(
                    `podcaster/stats/graph/downloads/${this.selectedPodcast}/${this.selectedYear}/${this.selectedMonth}`
                )
                .then((response) => {
                    this.series = [{ name: 'downloads', data: response.days.slice(1) }]
                })
        },
    },
    created() {
        this.getEpisodeGraphData()
    },
}
</script>
