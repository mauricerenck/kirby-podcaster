<template>
    <section class="k-modified-section podcaster">
        <apexchart height="300" :options="options" :series="series"></apexchart>
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
                    id: 'devices-month',
                    type: 'pie',
                },
                theme: { mode: this.themeMode, palette: 'palette3' },
            },

            series: [],
        }
    },
    watch: {
        selectedMonth() {
            this.getDeviceGraphData()
        },
        selectedYear() {
            this.getDeviceGraphData()
        },
        selectedPodcast() {
            this.getDeviceGraphData()
        },
    },
    methods: {
        getDeviceGraphData() {
            this.$api
                .get(`podcaster/stats/graph/devices/${this.selectedPodcast}/${this.selectedYear}/${this.selectedMonth}`)
                .then((response) => {
                    if (!response) {
                        return
                    }

                    const devices = []
                    const labels = []

                    response.data.forEach((device) => {
                        devices.push((100 / response.total) * device.downloads)
                        labels.push(device.device)
                    })

                    this.series = devices
                    this.options = { ...this.options, ...{ labels: labels } }
                })
        },
    },
    created() {
        this.getDeviceGraphData()
    },
}
</script>
