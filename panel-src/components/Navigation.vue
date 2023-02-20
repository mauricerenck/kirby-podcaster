<template>
<div>
      <k-select-field
          v-model="selectedPodcast"
          :options="podcastOptions"
          label="Podcast"
          name="select"
          @input="setPodcast"
      />

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
</div>
</template>


<script>
export default {
  props: {
    selectedMonth: Number,
    selectedYear: Number,
    selectedPodcast: [String],
    podcastOptions: Array,
    setPodcast: Function,
    setYear: Function,
    setMonth: Function,
  },
}
</script>
