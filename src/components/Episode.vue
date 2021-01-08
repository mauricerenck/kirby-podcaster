<template>
  <section class="k-modified-section">

    <k-text>{{ error }}</k-text>
    <k-headline size="large">{{ headline }}</k-headline>
    <k-list :items="episodes" />

  </section>
</template>

<script>
import getMonth from 'date-fns/getMonth'
import addMonths from 'date-fns/addMonths'
import subMonths from 'date-fns/subMonths'
import getYear from 'date-fns/getYear'

export default {
    data: function() {
        return {
            currentDate: new Date(),
            currentMonthName: null,
            currentMonth: null,
            currentYear: null,
            headline: null,
            episodes: [],
            error: null,
            podcasterSlug: null
        }
    },
    created: function() {
        this.setNewDateVars()
    },
    mounted() {
        this.podcasterSlug = this.pageValues.podcastid
        this.getStats()
    },
    computed: {
        pageValues() {
            return this.$store.getters['content/values'](this.id)
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
        getStats() {
            fetch('/api/podcaster/stats/'+ this.podcasterSlug + '/year/' + this.currentYear + '/month/' + (this.currentMonth+1), {
                method: 'GET',
                headers: {
                    'X-CSRF': panel.csrf,
                },
            })
                .then(response => {
                     if(response.status !== 200) {
                        throw 'You are tracking your downloads, using the file method. Stats are currently available only when using mysql'
                    }

                    return response
                })
                .then(response => response.json())
                .then(response => {
                      this.episodes = this.computeStats(response.stats)
                })
                .catch(error => {
                    this.error = error
                    console.log(this.error)
                })
        },
        computeStats(stats) {
            const episodeStats = stats.episodes.map(function(episode) {
                return { text: episode.episode_name, info: episode.downloaded }
            })

            return episodeStats
        },
        prevMonth() {
            const newDate = subMonths(this.currentDate, 1)
            this.currentDate = newDate
            this.setNewDateVars()
        },
        nextMonth() {
            const newDate = addMonths(this.currentDate, 1)
            this.currentDate = newDate
            this.setNewDateVars()
        },
        setNewDateVars() {
            this.currentMonth = getMonth(this.currentDate)
            this.currentYear=  getYear(this.currentDate)
            this.currentMonthName = this.currentDate.toLocaleString('en', { month: 'long' })
            this.headline = 'Stats for ' + this.currentMonthName + ' ' + this.currentYear
        },
    },
}
</script>

<style lang="scss">
.k-section-name-podstatsEpisodic {
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