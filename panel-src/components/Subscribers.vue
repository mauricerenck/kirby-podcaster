<template>
  <k-stats :reports="[reports]"/>
</template>
<script>
export default {
  props: {
    selectedPodcast: String,
  },
  computed: {
    reports() {
      return {
        label: 'Estimated Subscribers', value :this.estimated
      }
    }
  },
  data() {
    return {
      estimated: 0,
    }
  },
  methods: {
    getReports() {
      this.$api
          .get(`podcaster/stats/subscribers/${this.selectedPodcast}`)
          .then((response) => {
            this.estimated = response.estSubscribers
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