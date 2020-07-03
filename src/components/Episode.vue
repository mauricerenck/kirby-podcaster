<template>
  <section class="k-modified-section">
    <div class="podcaster-prev-next">
        <button class="k-link k-button" v-on:click="prevMonth"><k-icon type="angle-left"></button>
        <button class="k-link k-button" v-on:click="nextMonth"><k-icon type="angle-right"></button>
    </div>
    <k-text>{{ error }}</k-text>
    <k-headline>{{ headline }}</k-headline>
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
        this.podcasterSlug = this.sanitizeTitle(this.pageValues.podcastertitle)
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
                return { text: episode.episode.replace(/-/g, ' '), info: episode.downloaded }
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
                sanitizeTitle: function(title) {
            var slug = ''
            // Change to lower case
            var titleLower = title.toLowerCase()
            // Letter "e"
            slug = titleLower.replace(/e|é|è|ẽ|ẻ|ẹ|ê|ế|ề|ễ|ể|ệ/gi, 'e')
            // Letter "a"
            slug = slug.replace(/a|á|à|ã|ả|ạ|ă|ắ|ằ|ẵ|ẳ|ặ|â|ấ|ầ|ẫ|ẩ|ậ/gi, 'a')
            // Letter "o"
            slug = slug.replace(/o|ó|ò|õ|ỏ|ọ|ô|ố|ồ|ỗ|ổ|ộ|ơ|ớ|ờ|ỡ|ở|ợ/gi, 'o')
            // Letter "u"
            slug = slug.replace(/u|ú|ù|ũ|ủ|ụ|ư|ứ|ừ|ữ|ử|ự/gi, 'u')
            // Letter "d"
            slug = slug.replace(/đ/gi, 'd')
            // Trim the last whitespace
            slug = slug.replace(/\s*$/g, '')
            // Change whitespace to "-"
            slug = slug.replace(/\s+/g, '-')

            return slug
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