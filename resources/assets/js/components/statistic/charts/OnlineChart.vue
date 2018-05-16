<script>
    import { Bar } from 'vue-chartjs'

    export default {
        extends: Bar,

        data: function() {
            return {
                labels: [],
                data:   [],
            }
        },

        mounted () {

            var app = this;
            axios.get('/api/statistic/' + app.$route.params.person_id).then(function (response) {
                app.labels = response.data.labels;
                app.data   = response.data.data;

                app.renderChart(
                    {
                        labels: app.labels,
                        datasets: [
                            {
                                label: 'online',
                                backgroundColor: '#f87979',
                                data: app.data
                            }
                        ]
                    },
                    {
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
                    }
                )
            });
        }
    }
</script>