<template>
    <div>
        <div v-if="user" class="row pt-3">
            <div class="col-9 clearfix">
                <div class="float-left"><img :src="user.photo_50"></div>
                <div class="float-left ml-2">{{ user.first_name }} <br> {{ user.last_name }}</div>
            </div>
            <div class="col-3 clearfix">
                <div v-if="!error_message" class="float-right">
                    <a v-if="is_statistic_exists" v-on:click="deleteFriend(user.id)" class="btn btn-outline-danger" role="button">Не следить</a>
                    <a v-else v-on:click="addFriend(user.id)" class="btn btn-outline-primary" role="button">Следить</a>
                </div>
            </div>
        </div>
        <div v-if="error_message" class="alert alert-danger my-3" role="alert">
            <small>{{ error_message }}</small>
        </div>
        <div v-if="is_info_message" class="alert alert-info my-3" role="alert">
            <small>Поздравляем, Вы начали слежение за пользователем! Пока здесь нет статистики, но она <b>начнет появляться</b> в ближайшее время. <b>Вернитесь</b> на сайт
                через <b>несколько часов</b> и вы увидите сколько времени Ваш друг сидит Вконтакте, а также кого он добавляет или удаляет из друзей</small>
        </div>
        <div class="mt-5">
            <span v-if="start_monitoring_rus" class="text-info"><small>Начало сбора статистики: <em>{{ start_monitoring_rus }}</em></small></span>
        </div>
        <div v-if="is_statistic_exists">
            <ul class="nav nav-tabs mt-3 mb-5">
                <li class="nav-item">
                    <router-link class="nav-link"
                                 :to="{ name: 'person_visits_statistic' }"
                                 v-bind:class="{'active': this.$route.name == 'person_visits_statistic'}">Активность</router-link>
                </li>
                <li class="nav-item">
                    <router-link class="nav-link"
                                 :to="{ name: 'person_friends_statistic' }"
                                 v-bind:class="{'active': this.$route.name == 'person_friends_statistic'}">
                        Новые друзья
                        <template v-if="unshowFriendsStatistic[user.id]">
                            <sup v-if="unshowFriendsStatistic[user.id]['add']" class="badge badge-pill badge-success">+{{ unshowFriendsStatistic[user.id]['add'] }}</sup>
                            <sup v-if="unshowFriendsStatistic[user.id]['delete']" class="badge badge-pill badge-danger">-{{ unshowFriendsStatistic[user.id]['delete'] }}</sup>
                        </template>
                    </router-link>
                </li>
            </ul>
            <router-view name="statisticVisits"></router-view>
            <router-view name="statisticFriends"></router-view>
        </div>
    </div>
</template>

<script>
    import {errorsMixin} from './mixins/errors.js'

    import StatisticVisits  from './statistic/StatisticVisitsComponent.vue';
    import StatisticFriends from './statistic/StatisticFriendsComponent.vue';

    export default {

        mixins: [
            errorsMixin,
        ],

        components: {
            StatisticVisits,
            StatisticFriends,
        },

        props: [
            'person_id',
        ],

        data: function () {
            return {
                user: null,
                is_statistic_exists: null,
                unshowFriendsStatistic: [],
                start_monitoring_rus: null,

                error_message: null,
                is_info_message: false,
            }
        },

        mounted () {
            var app = this;
            axios.get('/api/friend/' + app.person_id).then(function (response) {
                app.user                   = response.data.user;
                app.is_statistic_exists    = response.data.is_statistic_exists;
                app.unshowFriendsStatistic = response.data.unshow_friend_statistic;
                app.start_monitoring_rus   = response.data.start_monitoring_rus;
            });
        },

        methods: {
            addFriend(id) {
                var app = this;
                axios.post('/api/friend/' + id)
                    .then(function (response) {
                        app.is_statistic_exists = true;
                        app.is_info_message = true;
                    })
                    .catch(error => {
                        this.error_message = this.getTextError(error.response.status, error.response.data.error_message);
                    });

            },
            deleteFriend(id) {
                var app = this;
                axios.delete('/api/friend/' + id).then(function (response) {
                    app.is_statistic_exists  = false;
                    app.start_monitoring_rus = false;
                });
            },
        }
    }
</script>