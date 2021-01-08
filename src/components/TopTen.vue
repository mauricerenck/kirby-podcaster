<template>
  <section class="k-modified-section">
    <k-text>{{ error }}</k-text>
    <k-headline size="large">{{headline}}</k-headline>

    <k-list :items="topEpisodes" />

  </section>
</template>

<script>
import sanitizeTitle from "../utils";

export default {
  data: function() {
    return {
      limit: 10,
      headline: null,
      topEpisodes: [],
      error: null,
      podcasterSlug: null
    };
  },
  mounted() {
    this.podcasterSlug = this.pageValues.podcastid;
    this.getStats();
  },
  created: function() {
    this.load().then(response => {
      this.headline = response.headline;
    });
  },
  computed: {
    pageValues() {
      return this.$store.getters["content/values"](this.id);
    }
  },
  methods: {
    getStats() {
      fetch(
        "/api/podcaster/stats/" + this.podcasterSlug + "/top/" + this.limit,
        {
          method: "GET",
          headers: {
            "X-CSRF": panel.csrf
          }
        }
      )
        .then(response => {
          if (response.status !== 200) {
            throw "You are tracking your downloads, using the file method. Stats are currently available only when using mysql";
          }

          return response;
        })
        .then(response => response.json())
        .then(response => {
          this.topEpisodes = this.computeStats(response.stats);
        })
        .catch(error => {
          this.error = error;
        });
    },
    computeStats(stats) {
      const episodeStats = stats.map(function(episode) {
        return {
          text: episode.episode_name,
          info: episode.downloaded,
          icon: {
            type: "file",
            back: "black"
          }
        };
      });

      return episodeStats;
    }
  }
};
</script>

<style lang="scss">
.k-section-name-podstatsTop {
  table {
    width: 100%;
    border: 1px solid #ccc;
    background: #fff;
    margin-top: 0.5em;
  }

  td {
    border-bottom: 1px solid #ccc;
    line-height: 2;
    padding: 0 10px;
  }

  td:first-child {
    text-align: right;
  }

  .podcaster-prev-next {
    display: inline;
    text-align: right;
    color: #666;
  }

  .k-headline {
    display: inline;
  }

  .k-text {
    color: red;
  }
}
</style>