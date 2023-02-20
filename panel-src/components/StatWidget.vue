<template>
  <k-grid gutter="small">
    <k-column width="1/4"/>
    <k-column v-for="(report, index) in reports" :key="index" width="1/4">
      <k-stats :reports="[report]"/>
    </k-column>
  </k-grid>
</template>
<script>
export default {
  props: {
    selectedPodcast: String,
  },
  computed: {
    reports() {
      return [
        {label: 'Today', value: this.metricDay},
        {label: 'This week', value: this.metricWeek},
        {label: 'This month', value: this.metricMonth},
      ]
    }
  },
  data() {
    return {
      metricDay: 0,
      metricWeek: 0,
      metricMonth: 0,
    }
  },
  methods: {
    getReports() {
      this.$api
          .get(`podcaster/stats/quickreports/${this.selectedPodcast}`)
          .then((response) => {
            this.metricDay = response.reports.day
            this.metricWeek = response.reports.week
            this.metricMonth = response.reports.month
          })
    },
  },
  created() {
    this.getReports();
  },
  watch: {
    selectedPodcast() {
      this.getReports();
    }
  },
}
</script>