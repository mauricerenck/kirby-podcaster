<template>
    <section class="k-modified-section podcaster">
        <k-headline size="large" class="spacing">{{ headline }}</k-headline>

        <PodcasterList :items="topEpisodes" />

        <k-info-field :text="this.error" theme="negative" v-if="this.error !== null" />
        <k-info-field text="this.error" theme="negative" v-if="topEpisodes.length === 0" />
    </section>
</template>

<script>
export default {
    data: function() {
        return {
            limit: 10,
            headline: null,
            topEpisodes: [],
            error: null,
            podcasterSlug: null,
        }
    },
    mounted() {
        this.podcasterSlug = this.pageValues.podcastid
        this.getStats()
    },
    created: function() {
        this.load().then(response => {
            this.headline = response.headline
        })
    },
    computed: {
        pageValues() {
            return this.$store.getters['content/values'](this.id)
        },
    },
    methods: {
        getStats() {
            fetch('/api/podcaster/stats/' + this.podcasterSlug + '/top/' + this.limit, {
                method: 'GET',
                headers: {
                    'X-CSRF': !panel.csrf ? this.$system.csrf : panel.csrf,
                },
            })
                .then(response => {
                    if (response.status !== 200) {
                        throw 'Could not get data from API.'
                    }

                    return response
                })
                .then(response => response.json())
                .then(response => {
                    this.topEpisodes = response.stats.map(episode => {
                        return {
                            text: episode.episode_name,
                            downloads: episode.downloaded,
                            icon: {
                                type: 'file',
                                back: 'black',
                            },
                        }
                    })
                })
                .catch(error => {
                    this.error = error
                })
        },

        renderEpisodes() {},
    },
}
</script>

<style lang="scss">
.podcaster {
    .spacing {
        margin-bottom: var(--spacing-6);
    }

    .podcaster-prev-next {
        display: inline;
        text-align: right;
        color: #666;
    }
}
</style>
