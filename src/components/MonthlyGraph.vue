<template>
    <section class="k-modified-section">

        <div class="podcaster-prev-next">
            <button class="k-link k-button" v-on:click="prevMonth"><k-icon type="angle-left"/></button>
            <button class="k-link k-button" v-on:click="nextMonth"><k-icon type="angle-right"/></button>
        </div>
        {{error}}

        <k-grid gutter="large">
            <k-column width="2/3">
                <k-headline size="large">{{ headline }}</k-headline>
                <div class="chartWrapper">
                    <line-chart v-if="loaded" :chartdata="chartdata" :options="chartoptions" :styles="chartStyles"/>
                </div>
            </k-column>
            <k-column width="1/3">
                <div class="episodes-prev-next">
                    <button class="k-link k-button" v-on:click="prevEpisodes"><k-icon type="angle-left"/></button>
                    <button class="k-link k-button" v-on:click="nextEpisodes"><k-icon type="angle-right"/></button>
                </div>
                <k-headline size="large">Episodes</k-headline>
                <k-list :items="episodeList" />
            </k-column>
        </k-grid>
        <div class="spacer"></div>
        <k-grid gutter="large">
            <k-column width="1/3">
                <k-headline size="large">Devices</k-headline>
                <k-list :items="metaDevices" />
            </k-column>
            <k-column width="1/3">
                <k-headline size="large">Systems</k-headline>
                <k-list :items="metaOs" />
            </k-column>
            <k-column width="1/3">
                <k-headline size="large">UserAgents</k-headline>
                <k-list :items="metaUseragents" />
            </k-column>
        </k-grid>

      </section>
</template>

<script>
import getYear from 'date-fns/getYear'
import { getMonth } from 'date-fns/esm'
import { getDaysInMonth } from 'date-fns'
import addMonths from 'date-fns/addMonths'
import subMonths from 'date-fns/subMonths'
import LineChart from './Chart.vue'

export default {
    name: 'MonthyCharts',
    components: { LineChart },
    data() {
        return {
            headline: null,
            podcasterSlug: null,
            currentDate: new Date(),
            currentYear: null,
            currentMonth: null,
            currentDaysInMonth: null,
            episodes: [],
            episodeList: [],
            episodePage: [],
            metaDevices: [],
            metaOs: [],
            metaUseragents: [],
            chartdata: {},
            chartoptions: {
                responsive: true,
                maintainAspectRatio: false
            },
            loaded: false,
        }
    },
    created: function() {
        this.setNewDateVars()
    },

    mounted() {
        this.podcasterSlug = this.pageValues.podcastid
        this.setNewDateVars()
    },
    computed: {
        pageValues() {
            return this.$store.getters['content/values'](this.id)
        }
    },
        watch: {
        currentMonth: {
            immediate: false,
            handler(newVal, oldVal) {
                this.getStats()
            },
        }
    },
    methods: {
        getStats() {
            this.loaded = false

            fetch('/api/podcaster/stats/' + this.podcasterSlug + '/year/' + this.currentYear + '/month/' + this.currentMonth, {
                method: 'GET',
                headers: {
                    'X-CSRF': panel.csrf,
                },
            })
                .then(response => {
                    if (response.status !== 200) {
                        throw 'You are tracking your downloads, using the file method. Stats are currently available only when using mysql'
                    }

                    return response
                })
                .then(response => response.json())
                .then(response => {
                    this.addChartData(response.stats)
                })
                .catch(error => {
                    this.error = error
                })
        },
        addChartData(stats) {
            const chartValues = new Array(this.currentDaysInMonth).fill(0);
            const labels = new Array(this.currentDaysInMonth)

            for (let index = 0; index < labels.length; index++) {
                labels[index] = `${index+1}.`;
            }

            stats.graphData.episodes.map((episode) => {
                if(!episode.log_date) {
                    return
                }

                const episodeDate = episode.log_date.split('-');
                const statsDay = parseInt(episodeDate[2])-1;
                chartValues[statsDay] += parseInt(episode.downloaded)
            })

            this.chartdata = {
                labels: labels,
                datasets: [
                    {
                        backgroundColor: 'rgba(93, 128, 13, 0.7)',
                        borderColor: '#5d800d',
                        pointBorderColor: '#ffffff',
                        pointBackgroundColor: '#5d800d',
                        borderWidth: 1,
                        label: 'Downloads',
                        data: chartValues
                    }
                ]
            }
            this.loaded = true

            this.episodes = []
            stats.episodeData.episodes.map((episode) => {
                if(!episode.log_date) {
                    return
                }

                this.episodes.push({ text: episode.episode_name, info: episode.downloaded })
            })

            this.episodePage = 0
            this.episodeList = this.episodes.slice(this.episodePage,10)

            this.metaDevices = []
            stats.userAgents.devices.map((device) => {
                this.metaDevices.push({ text: device.device, info: device.downloaded })
            })

            this.metaOs = []
            stats.userAgents.os.map((os) => {
                this.metaOs.push({ text: os.os, info: os.downloaded })
            })

            this.metaUseragents = []
            stats.userAgents.useragents.map((ua) => {
                this.metaUseragents.push({ text: ua.useragent, info: ua.downloaded })
            })


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
        prevEpisodes() {
            if(this.episodePage > 0) {
                this.episodePage-=10;
            }
            this.episodeList = this.episodes.slice(this.episodePage,this.episodePage+10)
        },
        nextEpisodes() {
            if(this.episodePage < this.episodes.length) {
                this.episodePage+=10;
            }
            this.episodeList = this.episodes.slice(this.episodePage,this.episodePage+10)
        },
        setNewDateVars() {
            this.currentMonth = getMonth(this.currentDate) +1
            this.currentYear = getYear(this.currentDate)
            this.currentDaysInMonth = getDaysInMonth(this.currentDate)
            this.currentMonthName = this.currentDate.toLocaleString('en', { month: 'long' })

            this.headline = 'Stats for ' + this.currentMonthName + ' ' + this.currentYear
        },
    },
}
</script>

<style lang="scss">
.chartWrapper {
    position: relative;
    height: 400px;
}

.spacer {
    padding: 30px;
}
</style>