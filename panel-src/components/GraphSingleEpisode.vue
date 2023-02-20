<template>
  <section class="k-modified-section podcaster">
    <apexchart height="300" type="line" :options="options" :series="series"></apexchart>
  </section>
</template>
<script>
import VueApexCharts from 'vue-apexcharts'

export default {
  components: {'apexchart': VueApexCharts},
  props: {
    selectedPodcast: String,
    selectedEpisode: String,
  },
  data() {
    return {
      options: {
        chart: {
          id: 'episode-details'
        },
        stroke: {
          curve: 'smooth',
          width: 2,
        },
        yaxis: {
          labels: {
            formatter: function (val) {
              return val.toFixed(0);
            }
          }
        },
        theme: {
          palette: 'palette3'
        },
        xaxis: {
          categories: ['a', 'b']
        }
      },
      series: []
    }
  },
  watch: {
    selectedEpisode() {
      this.getEpisodeGraphData();
    },
    selectedPodcast() {
      this.getEpisodeGraphData();
    }
  },
  methods: {
    getEpisodeGraphData() {
      this.$api
          .get(`podcaster/stats/graph/episode/${this.selectedPodcast}/${this.selectedEpisode}`)
          .then((response) => {
            const data = [];

            response.forEach(day => {
              data.push({x: day.date, y: day.downloads})
            })

            this.series = [{name: 'downloads', data: data}]
          })
          },
    },
    created() {
      this.getEpisodeGraphData();
    },

  }
</script>
