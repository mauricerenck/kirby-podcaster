<template>
  <div>
    <k-collection layout="list" :items="episodes" @item="setEpisode"/>
  </div>
</template>
<script>
export default {
  props: {
    selectedPodcast: String,
    onSelectEpisode: Function
  },
  data() {
    return {
      episodes: []
    }
  },
  methods: {
    getTopEpisodes() {
      this.$api
          .get(`podcaster/stats/top-episodes/${this.selectedPodcast}`)
          .then((response) => {
            this.episodes = response.map(episode => {
              return {
                text: episode.title,
                slug: episode.slug,
                info: episode.downloads
              }
            })
          })
    },
    setEpisode(selection) {
      if (!selection.slug) {
        return
      }

      this.onSelectEpisode(selection.slug)
    }
  },
  created() {
    this.getTopEpisodes();
  },

  watch: {
    selectedPodcast() {
      this.getTopEpisodes();
    },
    selectedMonth() {
      this.getTopEpisodes();
    },
    selectedYear() {
      this.getTopEpisodes();
    }

  },
}
</script>