<template>
    <k-grid style="gap: var(--spacing-6)">
        <k-column v-for="(report, index) in reports" :key="index" width="1/6">
            <k-stats :reports="[report]" />
        </k-column>
    </k-grid>
</template>
<script>
export default {
    props: {
        selectedPodcast: String,
    },
    computed: {
        reports() {
            return [
                { label: 'Today', value: this.metricDay },
                { label: 'This week', value: this.metricWeek },
                { label: 'This month', value: this.metricMonth },
                { label: 'Overall', value: this.metricOverall },
                { label: 'Estimated Subscribers', value: this.estimated },
            ]
        },
    },
    data() {
        return {
            metricDay: '0',
            metricWeek: '0',
            metricMonth: '0',
            metricOverall: '0',
            estimated: 0,
            userLocal: navigator.language,
        }
    },
    methods: {
        formatNumber(metric) {
            const number = parseInt(metric)
            return number.toLocaleString(this.userLocal, { style: 'decimal' })
        },
        getReports() {
            this.$api.get(`podcaster/stats/quickreports/${this.selectedPodcast}`).then((response) => {
                this.metricDay = this.formatNumber(response.reports.day)
                this.metricWeek = this.formatNumber(response.reports.week)
                this.metricMonth = this.formatNumber(response.reports.month)
                this.metricOverall = this.formatNumber(response.reports.overall)
            })

            this.$api.get(`podcaster/stats/subscribers/${this.selectedPodcast}`).then((response) => {
                this.estimated = this.formatNumber(response.estSubscribers)
            })
        },
    },
    created() {
        this.getReports()
    },
    watch: {
        selectedPodcast() {
            this.getReports()
        },
    },
}
</script>
