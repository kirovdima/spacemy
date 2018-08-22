<template>
    <div>
        <div class="mb-2 ml-4">
            <button type="button" class="btn btn-outline-success btn-sm m-1" v-on:click="setPeriod('day')" style="cursor: pointer">день</button>
            <button type="button" class="btn btn-outline-success btn-sm m-1" v-on:click="setPeriod('week')" style="cursor: pointer">неделя</button>
            <button type="button" class="btn btn-outline-success btn-sm m-1" v-on:click="setPeriod('month')" style="cursor: pointer">месяц</button>
        </div>
        <div class="mb-2 ml-4">
            <span v-on:click="setPrevStartDate()" style="cursor: pointer"><</span>
            <span>{{ humanStartDate }}</span>
            <span v-on:click="setNextStartDate()" style="cursor: pointer">></span>
        </div>
        <online-chart :maintainAspectRatio="false" :chart-data="data" v-bind:options="options"></online-chart>
    </div>
</template>

<script>
    import OnlineChart from './charts/OnlineChart.vue'

    import {dateMixin} from './../mixins/date.js'

    export default {

        mixins: [
            dateMixin
        ],

        components: {
            OnlineChart
        },

        data: function() {
            return {
                data: null,
                options: null,
                period: 'day',
                start_date: new Date(),
            }
        },

        computed: {
            formattedStartDate: function () {
                return this.start_date.toISOString().slice(0, 10)
            },

            humanStartDate: function() {
                switch (this.period) {
                    case 'day':
                        return this.humanDate(this.start_date);
                        break;
                    case 'week':
                        return this.humanWeek(this.start_date);
                        break;
                    case 'month':
                        return this.humanMonth(this.start_date);
                        break;
                }
            }
        },

        methods: {
            setPeriod(period) {
                this.period = period;
                let date = this.start_date;
                switch (this.period) {
                    case 'week':
                        date.setDate(date.getDate() - date.getDay() + 1);
                        break;
                    case 'month':
                        date.setDate(1);
                        break;
                }
                this.start_date = new Date(date.getFullYear(), date.getMonth(), date.getDate(), 12, 0, 0);

                this.getStatistic();
            },

            setNextStartDate() {
                let date = this.start_date;
                switch (this.period) {
                    case 'day':
                        date.setDate(date.getDate() + 1);
                        break;
                    case 'week':
                        date.setDate(date.getDate() + 7);
                        date.setDate(date.getDate() - date.getDay() + 1); // на начало недели
                        break;
                    case 'month':
                        date.setMonth(date.getMonth() + 1);
                        date.setDate(1); // на начало месяца
                        break;
                }
                this.start_date = new Date(date.getFullYear(), date.getMonth(), date.getDate(), 12, 0, 0);

                this.getStatistic();
            },

            setPrevStartDate() {
                let date = this.start_date;
                switch (this.period) {
                    case 'day':
                        date.setDate(date.getDate() - 1);
                        break;
                    case 'week':
                        date.setDate(date.getDate() - 7);
                        date.setDate(date.getDate() - date.getDay() + 1); // на начало недели
                        break;
                    case 'month':
                        date.setMonth(date.getMonth() - 1);
                        date.setDate(1); // на начало месяца
                        break;
                }
                this.start_date = new Date(date.getFullYear(), date.getMonth(), date.getDate(), 12, 0, 0);

                this.getStatistic();
            },

            getStatistic() {
                var app = this;
                axios.get('/api/statistic/' + app.$route.params.person_id + '/' + app.period + '/' + this.formattedStartDate).then(function (response) {

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
            this.getStatistic();
        }

    }
</script>