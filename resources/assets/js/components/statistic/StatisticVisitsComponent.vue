<template>
    <div>
        <div class="mb-3 ml-4">
            <button type="button" class="btn btn-sm btn-outline-success m-1" v-bind:class="{'active': period === 'day'}" v-on:click="setPeriod('day')" style="cursor: pointer">День</button>
            <button type="button" class="btn btn-sm btn-outline-success m-1" v-bind:class="{'active': period === 'week'}" v-on:click="setPeriod('week')" style="cursor: pointer">Неделя</button>
            <button type="button" class="btn btn-sm btn-outline-success m-1" v-bind:class="{'active': period === 'month'}" v-on:click="setPeriod('month')" style="cursor: pointer">Месяц</button>
        </div>
        <div class="mb-4 ml-4">
            <button type="button" class="btn btn-sm btn-outline-success m-1 px-2 py-1" v-bind:class="{ 'disabled' : !isLeftArrowShow }" v-on:click="setPrevStartDate()" style="cursor: pointer">
                <span class="font-weight-bold"><</span>
            </button>
            <span>{{ humanStartDate }}</span>
            <button type="button" class="btn btn-sm btn-outline-success m-1 px-2 py-1" v-if="isRightArrowShow" v-on:click="setNextStartDate()" style="cursor: pointer">
                <span class="font-weight-bold">></span>
            </button>
            <span v-if="wait" class="ml-5"><img width="36px" :src="'/images/loader.gif'"></span>
        </div>
        <online-chart v-bind:class="{ 'd-none': data === null }" :maintainAspectRatio="false" :chart-data="data" v-bind:options="options"></online-chart>
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
                wait: null,
                start_monitoring_date: new Date(),
            }
        },

        props: [
            'person_id',
        ],

        computed: {
            formattedStartDate: function () {
                return this.start_date.getFullYear() + '-' + (this.start_date.getMonth() + 1) + '-' + this.start_date.getDate();
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
            },

            isLeftArrowShow: function () {
                let start_date = new Date(this.start_date.getFullYear(), this.start_date.getMonth(), this.start_date.getDate());
                let start_monitoring_date = new Date(this.start_monitoring_date.getFullYear(), this.start_monitoring_date.getMonth(), this.start_monitoring_date.getDate());

                return start_date > start_monitoring_date;
            },

            isRightArrowShow: function () {
                switch (this.period) {
                    case 'day':
                        return this.start_date.getTime() + 24 * 3600 * 1000 < (new Date()).getTime();
                        break;
                    case 'week':
                        return this.start_date.getTime() + 7 * 24 * 3600 * 1000 < (new Date()).getTime();
                        break;
                    case 'month':
                        let dayInMonth = 32 - new Date((new Date()).getFullYear(), (new Date()).getMonth(), 32).getDate();
                        return this.start_date.getTime() + dayInMonth * 24 * 3600 * 1000 < (new Date()).getTime();
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
                this.start_date = new Date(date.getFullYear(), date.getMonth(), date.getDate(), 0, 0, 1);

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
                this.start_date = new Date(date.getFullYear(), date.getMonth(), date.getDate(), 0, 0, 1);

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
                this.start_date = new Date(date.getFullYear(), date.getMonth(), date.getDate(), 0, 0, 1);

                this.getStatistic();
            },

            getStatistic() {
                var app = this;
                app.wait = true;
                axios.get('/api/statistic/' + app.person_id + '/' + app.period + '/' + this.formattedStartDate).then(function (response) {

                    app.start_monitoring_date = new Date(response.data.start_monitoring_date);

                    app.data = {
                        labels: response.data.labels,
                        datasets: [
                            {
                                label: app.period === 'day' ? 'Минут онлайн' : 'Часов онлайн',
                                backgroundColor: '#f87979',
                                data: response.data.data
                            }
                        ]
                    };

                    app.options = {
                        maintainAspectRatio: false,
                        legend: {
                            display: true,
                        },
                        tooltips: {
                            enabled: false,
                        },
                        scales: {
                            yAxes: [{
                                ticks: {
                                    unit: app.period === 'day' ? 'minute' : 'hour',
                                    max: app.period === 'day' ? 60 : 24,
                                }
                            }]
                        }
                    };

                    app.wait = false;
                });
            }
        },

        mounted: function() {
            this.getStatistic();
        }

    }
</script>