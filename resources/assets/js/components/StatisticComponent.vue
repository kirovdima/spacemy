<template>
    <div>
        <div class="clearfix pt-3">
            <div class="float-left"><img :src="user.photo_50"></div>
            <div class="float-left ml-2">{{ user.first_name }} {{ user.last_name }}</div>
        </div>
        <div class="pt-3">
            <p class="text-center">Статистика активности</p>
        </div>
        <online-chart :maintainAspectRatio="false"></online-chart>
    </div>
</template>

<script>
    import OnlineChart from './charts/OnlineChart.vue'

    export default {
        components: { OnlineChart },


        data: function () {
            return {
                user: null,
            }
        },

        mounted() {
            var app = this;
            axios.get('/api/friend/' + app.$route.params.person_id).then(function (response) {
                app.user = response.data.user;
            });
        }
    }
</script>