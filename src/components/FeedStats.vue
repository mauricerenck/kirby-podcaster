<template>
  <div>
      {{error}}
    <div id="feedChart"></div>
  </div>
</template>
<script>
import getYear from 'date-fns/getYear'
import { Chart } from 'frappe-charts/dist/frappe-charts.esm.js'
import 'frappe-charts/dist/frappe-charts.min.css'
import sanitizeTitle from '../utils'

export default {
    components: { Chart },
    data() {
        return {
            podcasterSlug: null,
            currentDate: new Date(),
            currentYear: null,
            yearlyStats: [0],
            data: {
                labels: ['Jan', 'Feb', 'Mar', 'Apr', 'Mai', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
                datasets: [],
            },
        }
    },
    computed: {
        pageValues() {
            return this.$store.getters['content/values'](this.id)
        },
    },
    mounted() {
        const year = getYear(this.currentDate)
        this.podcasterSlug = sanitizeTitle(this.pageValues.podcastertitle)
        this.getStats(year)
    },
    methods: {
        getStats(year) {
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
                current: {
                    name: year,
                    values: [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0],
                },
                past: {
                    name: year - 1,
                    values: [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0],
                },
            }

            stats.map(function(entry) {
                if (entry.year === year) {
                    chartData.current.values[entry.month - 1] = entry.downloaded
                } else {
                    chartData.past.values[entry.month - 1] = entry.downloaded
                }
            })

            this.data.datasets = [chartData.current, chartData.past]

            this.drawChart()
        },
        drawChart() {
            const chart = new Chart('#feedChart', {
                title: 'Monthly Feed Downloads',
                data: this.data,
                type: 'line', // or 'bar', 'line', 'scatter', 'pie', 'percentage'
                height: 350,
                colors: ['blue', 'dark-grey'],
                lineOptions: {
                    hideLine: 0,
                    regionFill: 1,
                    hideDots: 0,
                },
                barOptions: {
                    spaceRatio: 0.25,
                },
            })
        },
    },
}
</script>

<style lang="scss">
.line-graph-path {
    stroke-width: 2px !important;
}
</style>