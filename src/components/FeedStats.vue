<template>
    <div>
        <k-headline size="large">{{headline}}</k-headline>
        {{error}}
        <div class="chartWrapper">
                <line-chart v-if="loaded" :chartdata="chartdata" :options="chartoptions" :styles="chartStyles"/>
            </div>
        </div>
</template>
<script>
import getYear from 'date-fns/getYear'
import LineChart from './Chart.vue'

export default {
    name: 'YearlyFeedCharts',
    components: { Chart,LineChart },
    data() {
        return {
            podcasterSlug: null,
            currentDate: new Date(),
            chartdata: {},
            chartoptions: {
                responsive: true,
                maintainAspectRatio: false
            },
            loaded: false,
        }
    },
    created: function() {
        this.load().then(response => {
            this.headline = response.headline;
        });
    },
    mounted() {
        const year = getYear(this.currentDate)
        this.podcasterSlug = this.pageValues.podcastid
        this.getStats(year)
    },
    computed: {
        pageValues() {
            return this.$store.getters['content/values'](this.id)
        },
    },
    methods: {
        getStats(year) {
            this.loaded = false

            fetch('/api/podcaster/stats/' + this.podcasterSlug + '/feed/yearly-downloads/' + year + '+' + (year - 1), {
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
                    this.addChartData(response.stats, year)
                })
                .catch(error => {
                    this.error = error
                })
        },
        addChartData(stats, year) {
            const chartData = {
                current: [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0],
                past: [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0],
            }

            stats.map(function(entry) {
                if(!entry.log_date) {
                    return
                }

                const entryDate = entry.log_date.split('-');
                const statsYear = parseInt(entryDate[0]);
                const statsMonth = parseInt(entryDate[1]);

                if (statsYear === year) {
                    chartData.current[statsMonth - 1] = entry.downloaded
                } else {
                    chartData.past[statsMonth - 1] = entry.downloaded
                }
            })


            this.chartdata = {
                labels: ['Jan', 'Feb', 'Mar', 'Apr', 'Mai', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
                datasets: [
                    {
                        backgroundColor: 'rgba(93, 128, 13, 0.7)',
                        borderColor: '#5d800d',
                        pointBorderColor: '#ffffff',
                        pointBackgroundColor: '#5d800d',
                        borderWidth: 1,
                        label: year,
                        data: chartData.current
                    },
                    {
                        backgroundColor: '#ccc',
                        borderColor: '#777',
                        pointBorderColor: '#ffffff',
                        pointBackgroundColor: '#777',
                        borderWidth: 1,
                        label: year-1,
                        data: chartData.past
                    }
                ]
            }
            this.loaded = true

        }
    },
}
</script>

<style lang="scss">
.line-graph-path {
    stroke-width: 2px !important;
}
</style>