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
      this.getUserAgentGraphData();
    },
    selectedYear() {
      this.getUserAgentGraphData();
    },
    selectedPodcast() {
      this.getUserAgentGraphData();
    }
  },
  methods: {
    getUserAgentGraphData() {
      this.$api
          .get(`podcaster/stats/graph/useragents/${this.selectedPodcast}/${this.selectedYear}/${this.selectedMonth}`)
          .then((response) => {

            if (!response) {
              return
            }

            const agents = []
            const labels = []

            response.data.forEach(agent => {
              agents.push(100/response.total * agent.downloads)
              labels.push(agent.useragent)
            })

            this.series = agents
            this.options = {...this.options, ...{labels: labels}}
          })
    },
  },
  created() {
    this.getUserAgentGraphData();
  },

}
</script>
