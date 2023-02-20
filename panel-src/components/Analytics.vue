<template>
  <k-inside>
    <k-view class="k-podcaster-view">
      <k-header>Podcaster Analytics</k-header>

            <k-select-field
          v-model="selectedPodcast"
          :options="podcastOptions"
          label="Podcast"
          name="select"
          @input="setPodcast"
      />

      <k-column>
        <k-headline size="huge">Monthly stats {{ selectedMonth }}/{{ selectedYear }}</k-headline>
      </k-column>
      <k-grid>
        <k-column width="1/4">
                <k-number-field
          v-model="selectedYear"
          :step="1"
          label="Year"
          @input="setYear"
      />

      <k-number-field
          v-model="selectedMonth"
          :max="12"
          :min="1"
          :step="1"
          label="Month"
          @input="setMonth"
      />
          </k-column>
<k-column width="3/4">
          <PodStatWidget :selectedPodcast="this.selectedPodcast"/>
</k-column>
        <k-column>
          <PodGraphEpisodesMonth
              :selectedMonth="this.selectedMonth"
              :selectedYear="this.selectedYear"
              :selectedPodcast="this.selectedPodcast"
          />
        </k-column>

        <k-column>
        <k-headline size="huge">Overall stats</k-headline>
        </k-column>

        <k-column width="2/3">
          <PodGraphSingleEpisode
              :selectedPodcast="this.selectedPodcast"
              :selectedEpisode="this.selectedEpisode"
          />
        </k-column>

        <k-column width="1/3">

        <PodEpisodesAutocomplete :selectedPodcast="this.selectedPodcast" :selectedEpisode="this.selectedEpisode" :onSelectEpisode="this.setEpisode"/>

          <PodTopEpisodes
              :selectedPodcast="this.selectedPodcast"
              :onSelectEpisode="this.setEpisode"
          />
        </k-column>

      </k-grid>


      Overall Downloads
      Estimated Subscribers

      UserAents
      Devices
      OS
      Location


      <DetailsByMonth :summary="summary"/>
      <Targets :targets="targets"/>
      <Sources :sources="sources"/>
      <Sent :outbox="sent"/>
    </k-view>
  </k-inside>
</template>

<script>

export default {
  props: {
    year: Number,
    month: Number,
    podcasts: [String],
    quickReports: Object,
  },
  data() {
    return {
      allEpisodes: [],
      selectedMonth: this.month,
      selectedYear: this.year,
      selectedPodcast: this.podcasts[0].id,
      selectedEpisode: null,
    }
  },
  computed: {
    podcastOptions() {
      return this.podcasts.map(podcast => {
        return {
          value: podcast.id,
          text: podcast.name,
        }
      });
    }
  },
  methods: {
    getDropDownMonths() {
      return [1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12]
    },

    setMonth(value) {
      this.selectedMonth = value > 13 ? 12 : value
    },

    setYear(value) {
      this.selectedYear = value > 1969 ? value : 1970
    },

    setPodcast(value) {
      this.selectedPodcast = value;
    },

    setEpisode(value) {
      this.selectedEpisode = value;
    },


  },
}
</script>

<style lang="scss"></style>
