<template>
    <k-panel-inside>
        <k-view class="k-podcaster-view">
            <k-header>Podcaster Analytics</k-header>

            <k-grid style="gap: var(--spacing-6)">
                <k-column width="1/4">
                    <k-select-field
                        v-model="selectedPodcast"
                        :options="podcastOptions"
                        label="Podcast"
                        name="select"
                        @input="setPodcast"
                    />
                </k-column>

                <k-column width="4/4">
                    <k-line-field />
                    <PodStatWidget :selectedPodcast="this.selectedPodcast" />
                    <k-line-field />
                </k-column>

                <k-column width="4/6">
                    <k-headline tag="h2">Monthly stats</k-headline>
                </k-column>

                <k-column width="1/6">
                    <k-number-field v-model="selectedYear" :step="1" label="Year" @input="setYear" />
                </k-column>
                <k-column width="1/6">
                    <k-number-field
                        v-model="selectedMonth"
                        :max="12"
                        :min="1"
                        :step="1"
                        label="Month"
                        @input="setMonth"
                    />
                </k-column>

                <k-column>
                    <PodGraphEpisodesMonth
                        :selectedMonth="this.selectedMonth"
                        :selectedYear="this.selectedYear"
                        :selectedPodcast="this.selectedPodcast"
                        :themeMode="this.apexMode()"
                    />
                </k-column>
            </k-grid>
            <k-grid class="podcaster podcaster-graph" style="gap: var(--spacing-6)">
                <k-column width="1/3">
                    <PodGraphDevices
                        :selectedMonth="this.selectedMonth"
                        :selectedYear="this.selectedYear"
                        :selectedPodcast="this.selectedPodcast"
                        :themeMode="this.apexMode()"
                    />
                </k-column>

                <k-column width="1/3">
                    <PodGraphUserAgents
                        :selectedMonth="this.selectedMonth"
                        :selectedYear="this.selectedYear"
                        :selectedPodcast="this.selectedPodcast"
                        :themeMode="this.apexMode()"
                    />
                </k-column>

                <k-column width="1/3">
                    <PodGraphSystems
                        :selectedMonth="this.selectedMonth"
                        :selectedYear="this.selectedYear"
                        :selectedPodcast="this.selectedPodcast"
                        :themeMode="this.apexMode()"
                    />
                </k-column>
            </k-grid>
            <k-grid style="gap: var(--spacing-6)">
                <k-column>
                    <k-headline tag="h2">Top 10 Episodes</k-headline>
                </k-column>
                <k-column>
                    <PodGraphTopEpisodes :selectedPodcast="this.selectedPodcast" :themeMode="this.apexMode()" />
                </k-column>
            </k-grid>
            <k-grid style="gap: var(--spacing-6)">
                <k-column>
                    <k-headline tag="h2">Episode Details</k-headline>
                </k-column>

                <k-column width="1/1">
                    <PodEpisodesAutocomplete
                        :selectedPodcast="this.selectedPodcast"
                        :selectedEpisodes="this.selectedEpisodes"
                        :onSelectedEpisodes="this.setSelectedEpisodes"
                    />

                    <PodGraphSingleEpisode
                        :selectedPodcast="this.selectedPodcast"
                        :selectedEpisodes="this.selectedEpisodes"
                        :themeMode="this.apexMode()"
                    />
                </k-column>

                <k-column width="1/1">
                    <k-headline tag="h2">Episode Downloads</k-headline>
                    <PodGraphEpisodes :selectedPodcast="this.selectedPodcast" :themeMode="this.apexMode()" />
                </k-column>
                <k-column width="1/1">
                    <k-headline tag="h2">Feed Downloads</k-headline>
                    <PodGraphFeeds :selectedPodcast="this.selectedPodcast" :themeMode="this.apexMode()" />
                </k-column>
            </k-grid>
        </k-view>
    </k-panel-inside>
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
            selectedEpisodes: [],
        }
    },
    computed: {
        podcastOptions() {
            return this.podcasts.map((podcast) => {
                return {
                    value: podcast.id,
                    text: podcast.name,
                }
            })
        },
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
            this.selectedPodcast = value
            this.selectedEpisodes = []
        },

        setEpisode(value) {
            this.selectedEpisode = value
        },

        setSelectedEpisodes(value) {
            this.selectedEpisodes = value
        },

        removeSelectedEpisode(value) {
            this.selectedEpisodes = this.selectedEpisodes.filter((episode) => {
                return episode !== value
            })
        },
        apexMode() {
            return window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light'
        },
    },
}
</script>

<style lang="css">
.podcaster-graph {
    background: var(--color-white);
    border-radius: var(--rounded-md);
    padding: var(--spacing-6);
    margin: var(--spacing-6) 0;
}

.k-podcaster-view h2 {
    margin-top: var(--spacing-10);
    font-size: var(--text-2xl);
}

@media (prefers-color-scheme: dark) {
    .podcaster-graph {
        background-color: rgb(66, 66, 66); /* apexchart dark theme */
    }
}
</style>
