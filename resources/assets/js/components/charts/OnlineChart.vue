<script>
    import { Line } from 'vue-chartjs'

    export default {
        extends: Line,

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

                app.renderChart({
                    labels: app.labels,
                    datasets: [
                        {
                            label: 'online',
                            backgroundColor: '#f87979',
                            data: app.data
                        }
                    ]
                })
            });
        }
    }
</script>