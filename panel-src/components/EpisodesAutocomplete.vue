<template>
    <div>
        <k-button icon="checklist" variant="filled" @click="$refs.episodeList.toggle()"> Select Episodes </k-button>
        <k-picklist-dropdown ref="episodeList" :options="this.episodes" @input="this.setEpisode" />
    </div>
</template>
<script>
export default {
    props: {
        selectedPodcast: String,
        selectedEpisodes: [String],
        onSelectedEpisodes: Function,
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
        test(value) {
            console.log('test', value)
        },
        search(search) {
            this.query = search.query

            this.searchResults = this.episodes.filter((episode) => {
                const title = episode.text ? episode.text.toLowerCase() : ''
                const slug = episode.slug ? episode.slug.toLowerCase() : ''

                return title.includes(search.query.toLowerCase()) || slug.includes(search.query.toLowerCase())
            })
        },
        getEpisodes() {
            if (!this.selectedPodcast) {
                return
            }

            this.$api.get(`podcaster/stats/episodes/${this.selectedPodcast}`).then((response) => {
                this.episodes = response.map((episode) => {
                    return {
                        value: episode.slug,
                        text: episode.title,
                        '@onCLick': this.test,
                    }
                })
            })
        },
        setEpisode(selection) {
            if (!selection) {
                return
            }

            this.onSelectedEpisodes(selection)
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
