<template>
  <section class="k-modified-section podcaster">
    <apexchart height="300" :options="options" :series="series"></apexchart>
  </section>
</template>
<script>
import VueApexCharts from 'vue-apexcharts'

export default {
  components: {'apexchart': VueApexCharts},
  props: {
    selectedMonth: Number,
    selectedYear: Number,
    selectedPodcast: String,
  },
  data() {
    return {
      options: {
        chart: {
          id: 'devices-month',
          type: 'pie'
        },
        theme: {
          palette: 'palette3'
        },
      },

      series: []
    }
  },
  watch: {
    selectedMonth() {
      this.getSystemsGraphData();
    },
    selectedYear() {
      this.getSystemsGraphData();
    },
    selectedPodcast() {
      this.getSystemsGraphData();
    }
  },
  methods: {
    getSystemsGraphData() {
      this.$api
          .get(`podcaster/stats/graph/os/${this.selectedPodcast}/${this.selectedYear}/${this.selectedMonth}`)
          .then((response) => {

            if (!response) {
              return
            }

            const systems = []
            const labels = []

            response.data.forEach(system => {
              systems.push(100/response.total * system.downloads)
              labels.push(system.os)
            })

            this.series = systems
            this.options = {...this.options, ...{labels: labels}}
          })
    },
  },
  created() {
    this.getSystemsGraphData();
  },

}
</script>
