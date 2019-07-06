<template>
  <section class="k-modified-section podcaster-import-wizard">
    <k-headline>{{ headline }}</k-headline>
    <div class="log">
        <ul>
            <li v-for="log in logs" :log="log" :key="log.id">{{log.msg}}</li>
        </ul>
    </div>
    <button class="k-button start-import" v-on:click="startImport">Start import</button>
  </section>
</template>


<script>
export default {
    data: function() {
        return {
            headline: null,
            logs: [],
            info: null
        }
    },
    created: function() {
    },
    mounted() {
        this.headline = this.pageValues.podcasterWizardSrcFeed
    },
    computed: {
        id() {
            return this.$store.state.form.current
        },
        pageValues() {
            return this.$store.getters['form/values'](this.id)
        },
    },
    watch: {
        currentDate: {
            immediate: false,
            handler(newVal, oldVal) {
                this.getStats()
            },
        }
    },
    methods: {
        startImport(event) {
            let feedUrl = this.pageValues.podcasterwizardsrcfeed;
            // event.target.style = 'display: none'
            this.log = []
            this.logs.push({id: 1, msg: 'Starting import…'})
            this.logs.push({id: 2, msg: 'Looking up: ' + feedUrl})
            this.getFeed(feedUrl)
        },
        getFeed(feedUrl) {
            fetch('/api/podcaster/wizard/checkfeed', {
                method: 'GET',
                headers: {
                    'X-CSRF': panel.csrf,
                    'X-FEED-URI': feedUrl
                },
            })
            .then(response => response.json())
            .then(response => {
                if(response.status === 'error') {
                    this.logs.push({id: 3, msg: 'Could not read feed'})
                }

                const numEpisodes = (typeof response.channel.item.length !== 'undefined') ? response.channel.item.lengt : 1;

                this.logs.push({id: 3, msg: 'Found feed for: ' + response.channel.title})
                this.logs.push({id: 4, msg: 'Found ' + numEpisodes + ' episodes'})
                this.importEpisodes(response.channel.item)
            })
            .catch(error => {
                this.error = error
                console.log(this.error)
            })
        },
        importEpisodes(items) {
            this.logs.push({id: 5, msg: ' '})

            if(typeof items.length === 'undefined') {
                const episode = {
                    title: items.title,
                    link: items.link,
                    pubDate: items.pubDate,
                    description: items.description,
                    itunessubtitle: items.itunessubtitle,
                    itunessummary: items.itunessummary,
                    itunesduration: items.itunesduration,
                    itunesseason: items.itunesseason,
                    itunesexplicit: items.itunesexplicit,
                    itunesblock:  items.itunesblock,
                    file: items.enclosure["@attributes"].url
                }

                this.createEpisode(items)
            } else {
                for (let i = items.length-1; i >= 0; i--) { 
                    this.createEpisode(items[i])
                }
            }
        },
        createEpisode(episode) {
            const episodeData = {
                title: episode.title,
                link: episode.link,
                pubDate: episode.pubDate,
                description: episode.description,
                itunessubtitle: episode.itunessubtitle,
                itunessummary: episode.itunessummary,
                itunesduration: episode.itunesduration,
                itunesseason: episode.itunesseason,
                itunesexplicit: episode.itunesexplicit,
                itunesblock:  episode.itunesblock,
                file: episode.enclosure["@attributes"].url
            }
            console.log('HHH', episodeData)

            fetch('/api/podcaster/wizard/createEpisode', {
                method: 'POST',
                headers: {
                    'X-CSRF': panel.csrf,
                    'X-TARGET-PAGE': this.pageValues.podcasterwizarddestination[0].id,
                    'X-PAGE-TEMPLATE': 'default',
                },
                body: JSON.stringify(episodeData)
            })
            .then(response => response.json())
            .then(response => {
                this.logs.push({id: (5), msg: 'created ' + response.title})
            })
        }
    },
}
</script>

<style lang="scss">
.podcaster-import-wizard {
    button {
        border: 1px solid green;
    }

    .log {
        background: #333;
        color: white;
        font-family: courier;
        font-size: 12px;

        ul {
            padding: 20px;
        }
    }
}
</style>