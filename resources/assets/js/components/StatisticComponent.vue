<template>
    <div>
        <div v-if="user" class="row pt-3">
            <div class="col-9 clearfix">
                <div class="float-left"><img :src="user.photo_50"></div>
                <div class="float-left ml-2">{{ user.first_name }} <br> {{ user.last_name }}</div>
            </div>
            <div class="col-3 clearfix">
                <div class="float-right">
                    <a v-if="is_statistic_exists" v-on:click="deleteFriend(user.id)" class="btn btn-outline-danger" role="button">Не следить</a>
                    <a v-else v-on:click="addFriend(user.id)" class="btn btn-outline-primary" role="button">Следить</a>
                </div>
            </div>
        </div>
        <div v-if="is_statistic_exists">
            <ul class="nav nav-tabs mt-5 mb-5">
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
    import StatisticVisits  from './statistic/StatisticVisitsComponent.vue';
    import StatisticFriends from './statistic/StatisticFriendsComponent.vue';

    export default {

        components: {
            StatisticVisits,
            StatisticFriends,
        },

        data: function () {
            return {
                user: null,
                is_statistic_exists: null,
                unshowFriendsStatistic: [],
            }
        },

        mounted () {
            var app = this;
            axios.get('/api/friend/' + app.$route.params.person_id).then(function (response) {
                app.user                   = response.data.user;
                app.is_statistic_exists    = response.data.is_statistic_exists;
                app.unshowFriendsStatistic = response.data.unshow_friend_statistic;
            });
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