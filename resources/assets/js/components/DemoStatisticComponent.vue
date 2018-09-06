<template>
    <div>
        <div v-if="user" class="row pt-3">
            <div class="col-9 clearfix">
                <div class="float-left"><img :src="user.photo_50"></div>
                <div class="float-left ml-2">{{ user.first_name }} <br> {{ user.last_name }}</div>
            </div>
        </div>
        <div class="mt-4"></div>
        <div v-if="error_message" class="alert alert-danger my-3" role="alert">
            <small>{{ error_message }}</small>
        </div>
        <div v-if="is_statistic_exists">
            <ul class="nav nav-tabs mt-3 mb-3">
                <li class="nav-item">
                    <router-link class="nav-link"
                                 :to="{ name: 'demo_visits_statistic' }"
                                 v-bind:class="{'active': this.$route.name == 'demo_visits_statistic'}">Активность</router-link>
                </li>
                <li class="nav-item">
                    <router-link class="nav-link"
                                 :to="{ name: 'demo_friends_statistic' }"
                                 v-bind:class="{'active': this.$route.name == 'demo_friends_statistic'}">
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