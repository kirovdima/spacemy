<template>
    <div>
        <a class="badge badge-light" v-on:click="setPeriod('day')" style="cursor: pointer">24 часа</a>
        <a class="badge badge-light" v-on:click="setPeriod('week')" style="cursor: pointer">неделя</a>
        <a class="badge badge-light" v-on:click="setPeriod('month')" style="cursor: pointer">месяц</a>
        <online-chart :maintainAspectRatio="false" :chart-data="data" v-bind:options="options"></online-chart>
    </div>
</template>

<script>
    import OnlineChart from './charts/OnlineChart.vue'

    export default {

        components: {
            OnlineChart
        },

        data: function() {
            return {
                data: null,
                options: null,
            }
        },

        methods: {
            setPeriod(period) {
                var app = this;
                axios.get('/api/statistic/' + app.$route.params.person_id + '/' + period).then(function (response) {

                    app.data = {
                        labels: response.data.labels,
                        datasets: [
                            {
                                label: 'online',
                                backgroundColor: '#f87979',
                                data: response.data.data
                            }
                        ]
                    };

                    app.options = {
                        maintainAspectRatio: false,
                        scales: {
                            yAxes: [{
                                ticks: {
                                    stepSize: 25,
                                    suggestedMin: 0,
                                    suggestedMax: 1,
                                    min: 0,
                                    max: 100,
                                }
                            }]
                        }
                    };
                });
            }
        },

        mounted: function() {
            this.setPeriod('day');
        }

    }
</script>