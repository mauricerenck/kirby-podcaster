<template>
  <div>
      {{error}}
    <div id="chart"></div>
  </div>
</template>
<script>
import getYear from 'date-fns/getYear'
import { Chart } from 'frappe-charts/dist/frappe-charts.esm.js'
import 'frappe-charts/dist/frappe-charts.min.css'

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
        id() {
            return this.$store.state.form.current
        },
        pageValues() {
            return this.$store.getters['form/values'](this.id)
        },
    },
    mounted() {
        const year = getYear(this.currentDate)
        this.podcasterSlug = this.sanitizeTitle(this.pageValues.podcastertitle)
        this.getStats(year)
    },
    methods: {
        getStats(year) {
            fetch('/api/podcaster/stats/' + this.podcasterSlug + '/yearly-downloads/' + year + '+' + (year - 1), {
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
            const chart = new Chart('#chart', {
                title: 'Monthly Episode Downloads',
                data: this.data,
                type: 'line', // or 'bar', 'line', 'scatter', 'pie', 'percentage'
                height: 250,
                colors: ['green', 'dark-grey'],
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
.line-graph-path {
    stroke-width: 2px !important;
}
</style>