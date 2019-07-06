<template>
  <section class="k-modified-section podcaster-import-wizard">
    <k-headline>{{ headline }}</k-headline>
    <div class="log">
        <div class="currentState">Processing &raquo;{{currentEpisode}}&laquo;</div>
        <div>Trying to parse &raquo;{{feedName}}&laquo;</div>
        <div>Found {{numItems}} episodes in feed</div>
        <div>creating pages, <strong>{{numRemain}}</strong> remaining</div>
        <div>{{numDownload}} audio downloads remaining</div>
        <div>{{failed}} failed attempts</div>
    </div>
    <button class="k-button start-import" v-on:click="startImport">1. Start import</button>
  </section>
</template>


<script>
export default {
    data: function() {
        return {
            headline: null,
            logs: [],
            feedItems: [],
            numItems: 0,
            numRemain: 0,
            numDownload: 0,
            failed: 0,
            feedName: '',
            currentEpisode: ''
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
    methods: {
        startImport(event) {
            let feedUrl = this.pageValues.podcasterwizardsrcfeed;
            this.feedName = feedUrl
            event.target.style = 'display: none'
            document.querySelector('.log').style = 'display: block'
            this.getFeed(feedUrl)
        },

        startAudioDownload() {
            if(this.feedItems.length > 0) {
                this.downloadAudio()
            } else {
                this.logs.push({id: 3, msg: 'done'})
            }
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
                    this.failed++
                }

                const numEpisodes = (typeof response.channel.item.length !== 'undefined') ? response.channel.item.length : 1;

                this.feedName = response.channel.title
                this.numItems = numEpisodes
                this.numRemain = numEpisodes
                this.numDownload = numEpisodes

                this.importEpisodes(response.channel.item)
            })
            .catch(error => {
                this.error = error
                this.failed++
            })
        },
        importEpisodes(items) {
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
            this.currentEpisode = episode.title

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

            fetch('/api/podcaster/wizard/createEpisode', {
                method: 'POST',
                headers: {
                    'X-CSRF': panel.csrf,
                    'X-TARGET-PAGE': this.pageValues.podcasterwizarddestination[0].id,
                    'X-PAGE-TEMPLATE': this.pageValues.podcasterwizardtemplate,
                },
                body: JSON.stringify(episodeData)
            })
            .then(response => response.json())
            .then(response => {
                if(typeof response.status !== 'undefined') {
                    this.numFailed++
                } else {
                    this.logs.push({id: (5), msg: 'created "' + response.title + '"'})
                    this.logs.push({id: (6), msg: 'Downloading audio'})
                    this.feedItems.push({title: response.title, slug: response.slug, file: response.file})

                    this.numRemain--

                    if(this.numRemain === 0) {
                        this.startAudioDownload();
                    }
                }
            })
            .catch(error => {
                console.log(error)
                    this.numFailed++
            })
        },
        downloadAudio() {

            const slug = this.feedItems[0].slug
            const file = this.feedItems[0].file

            this.feedItems.shift();

            fetch('/api/podcaster/wizard/createFile', {
                method: 'POST',
                headers: {
                    'X-CSRF': panel.csrf,
                    'X-TARGET-PAGE': slug,
                },
                body: JSON.stringify({'file': file})
            })
            .then(response => response.json())
            .then(response => {
                if(typeof response.status !== 'undefined') {
                    this.failed++;
                } else {
                    this.numDownload = this.feedItems.length
                }

                this.startAudioDownload()
            })
            .catch(error => {
                console.log(error)
                    this.failed++;
            })
        }
    },
}
</script>

<style lang="scss">
.podcaster-import-wizard {
    button {
        border: 1px solid green;
        padding: 5px 10px;
        background: white;
        margin: 5px;
    }

    .start-file-transfer {
        display: none;
    }

    .log {
        display: none;
        background: #fff;
        font-family: courier;
        font-size: 14px;

        div {
            padding: 10px 20px;
        }
        .currentState {
            font-weight: bold;
            background: #333;
            color: #fff;
        }
    }
}
</style>