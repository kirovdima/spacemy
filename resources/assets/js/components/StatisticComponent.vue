<template>
    <div>
        <div v-if="user" class="clearfix pt-3">
            <div class="float-left"><img :src="user.photo_50"></div>
            <div class="float-left ml-2">{{ user.first_name }} {{ user.last_name }}</div>
        </div>
        <div v-if="is_statistic_exists">
            <div class="mt-3 pt-3 border-top">
                <p class="text-center">Статистика активности</p>
            </div>
            <online-chart :maintainAspectRatio="false"></online-chart>
            <div class="mt-3 pt-3 border-top">
                <p class="text-center">Изменение списка друзей</p>
            </div>
            <div class="row mt-5">
                <div class="col text-center">Удаленные</div>
                <div class="col text-center">Добавленные</div>
            </div>
            <div v-for="list_change in friends_list_change" class="row">
                <div class="col mt-5">
                    <div v-for="user in list_change.delete" class="clearfix">
                        <div class="float-left"><img :src="user.photo_50"></div>
                        <div class="float-left ml-2">{{ user.first_name }} {{ user.last_name }}</div>
                    </div>
                </div>
                <div class="col mt-5">
                    <div v-for="user in list_change.add" class="clearfix">
                        <div class="float-left"><img :src="user.photo_50"></div>
                        <div class="float-left ml-2">{{ user.first_name }} {{ user.last_name }}</div>
                    </div>
                </div>
            </div>
            <div class="row mt-5">
                <div class="col text-center">Количество друзей пользователя: {{ first_friends_count }}</div>
            </div>
            <div class="row mt-5 pt-3 border-top justify-content-center align-items-center">
                <a v-on:click="deleteFriend(user.id)" class="btn btn-outline-danger" role="button">Прекратить слежение</a>
            </div>
        </div>
        <div class="row mt-3 justify-content-center align-items-center" v-else-if="user">
            <a v-on:click="addFriend(user.id)" class="btn btn-outline-primary" role="button">Начать слежение за пользователем</a>
        </div>
    </div>
</template>

<script>
    import OnlineChart from './charts/OnlineChart.vue'

    export default {
        components: { OnlineChart },


        data: function () {
            return {
                user: null,
                is_statistic_exists: null,
                first_friends_count: null,
                friends_list_change: null,
            }
        },

        mounted () {
            var app = this;
            axios.get('/api/friend/' + app.$route.params.person_id).then(function (response) {
                app.user                = response.data.user;
                app.is_statistic_exists = response.data.is_statistic_exists;
            });
            axios.get('/api/statistic/' + app.$route.params.person_id + '/friend').then(function (response) {
                app.first_friends_count = response.data.first_friends_count;
                app.friends_list_change = response.data.friends_list_change;
            })
        },

        methods: {
            addFriend(id) {
                var app = this;
                axios.post('/api/friend/' + id).then(function (response) {
                    app.is_statistic_exists = true;
                })
            },
            deleteFriend(id) {
                var app = this;
                axios.delete('/api/friend/' + id).then(function (response) {
                    app.is_statistic_exists = false;
                });
            },
        }
    }
</script>