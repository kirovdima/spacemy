<script>
    import { Bar, mixins } from 'vue-chartjs'

    export default {
        extends: Bar,

        mixins: [mixins.reactiveProp],

        props: ['options',],

        watch: {
            'options': {
                handler(newOption, oldOption) {
                    this.$data._chart.destroy();
                    this.renderChart(this.chartData, this.options);
                }
            }
        },

        mounted () {
            this.renderChart(this.chartData, this.options)
        }
    }
</script>