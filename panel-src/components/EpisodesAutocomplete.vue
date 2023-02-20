<template>
  <div>
    <k-autocomplete ref="autocomplete" :options="this.episodes" @select="setEpisode">
      <input type="text" @input="$refs.autocomplete.search($event.target.value)" :value="selectedEpisode"/>
    </k-autocomplete>

  </div>
</template>
<script>
export default {
  props: {
    selectedPodcast: String,
    selectedEpisode: String,
    onSelectEpisode: Function
  },
  data() {
    return {
      episodes: [],
    }
  },
  methods: {
    getEpisodes() {
      if (!this.selectedPodcast) {
        return
      }

      this.$api
          .get(`podcaster/stats/episodes/${this.selectedPodcast}`)
          .then((response) => {
            console.log(response)
            this.episodes = response.map(episode => {
              return {value: episode.slug, text: episode.title}
            })
          })
    },
    setEpisode(selection) {
      if (!selection.value) {
        return
      }

      this.onSelectEpisode(selection.value)
    }
  },
  created() {
    this.getEpisodes();
  },

  watch: {
    selectedPodcast() {
      this.getEpisodes();
    },

  },
}
</script>