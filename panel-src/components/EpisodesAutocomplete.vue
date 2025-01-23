<template>
    <div>
        <k-autocomplete ref="autocomplete" :options="this.episodes" @select="setEpisode">
            <input
                type="text"
                @input="$refs.autocomplete.search($event.target.value)"
                class="podcaster-auto-complete"
                placeholder="Search for an episode..."
            />
        </k-autocomplete>
        <div class="episode-selection">
            <k-headline>Selected Episodes</k-headline>
            <k-collection layout="list" :items="episodeItems" @item="removeEpisode" />
        </div>
    </div>
</template>
<script>
export default {
    props: {
        selectedPodcast: String,
        selectedEpisodes: [String],
        onSelectEpisode: Function,
        onRemoveEpisode: Function,
    },
    data() {
        return {
            episodes: [],
        }
    },
    computed: {
        episodeItems() {
            if (!this.selectedEpisodes) {
                return []
            }

            return this.selectedEpisodes.map((episode) => {
                const selection = this.episodes.find((item) => {
                    return item.value === episode
                })

                return selection
            })
        },
    },
    methods: {
        getEpisodes() {
            if (!this.selectedPodcast) {
                return
            }

            this.$api.get(`podcaster/stats/episodes/${this.selectedPodcast}`).then((response) => {
                this.episodes = response.map((episode) => {
                    return { value: episode.slug, text: episode.title }
                })
            })
        },
        setEpisode(selection) {
            if (!selection.value) {
                return
            }

            this.onSelectEpisode(selection.value)
        },
        removeEpisode(selection) {
            if (!selection.value) {
                return
            }

            this.onRemoveEpisode(selection.value)
        },
    },
    created() {
        this.getEpisodes()
    },

    watch: {
        selectedPodcast() {
            this.getEpisodes()
        },
    },
}
</script>

<style lang="scss">
.podcaster-auto-complete {
    width: 100%;
    border: 0;
    border: 1px solid var(--color-gray-500);
    padding: var(--field-input-padding);
    line-height: var(--field-input-line-height);
    border-radius: var(--rounded-md);
}

.episode-selection {
    margin-top: 1rem;
    margin-bottom: 1rem;
}
</style>
