<template>
    <div>
        <online-chart :maintainAspectRatio="false"></online-chart>
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

            }
        },

    }
</script>