<style>
    .friend-item:hover {
        background-color: #e6f3ff;
    }
</style>

<template>
    <div>
        <div class="py-2 px-2 text-right">
            <small class="text-secondary"><span>Список обновлен: </span><span><em> {{ updated_at }}</em></span></small>
        </div>
        <div v-for="(friends, letter) in vkFriends" class="py-4 px-2">
            <div v-if="letter != 'has_stat'" class="row border-bottom border-info my-2">
                <div class="col"><h6 class="text-info">{{ letter }}</h6></div>
            </div>
            <div v-for="friend in friends" :key="friend.id" v-on:click="goToFriend(friend.id)" class="friend-item row py-2" style="cursor:pointer">
                <div class="col-2 col-lg-1"><img :src="friend.photo_50"></div>
                <div class="col-8 col-lg-10">
                    <div class="float-left">{{ friend.first_name }}<br>{{ friend.last_name }}</div>
                    <div v-if="userFriendIds.includes(friend.id)" class="float-right">
                        <div v-if="friend.id in todayFriendsStatistic" class="row m-1">
                            <div class="col">
                                <span v-if="todayFriendsStatistic[friend.id]['add']" class="badge badge-pill badge-success float-right">
                                    +{{ todayFriendsStatistic[friend.id]['add'] }} друзей
                                </span>
                                <span v-if="todayFriendsStatistic[friend.id]['delete']" class="badge badge-pill badge-danger float-right">
                                    -{{ todayFriendsStatistic[friend.id]['delete'] }} друзей
                                </span>
                            </div>
                        </div>
                        <div class="row m-1">
                            <div class="col">
                                <small><em>
                                    <span v-if="friend.id in todayStatistic" class="text-info">{{ todayStatistic[friend.id] }}</span>
                                    <span v-else="" class="text-secondary">offline</span>
                                </em></small>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-2 col-lg-1"><div v-if="userFriendIds.includes(friend.id)" class="imgContainer"><img :src="'images/barchart.png'"></div></div>
            </div>
        </div>
    </div>
</template>

<script>
    export default {
        data: function () {
            return {
                vkFriends:      [],
                userFriendIds:  [],
                todayStatistic: [],
                todayFriendsStatistic: [],
                updated_at:     null,
            }
        },
        mounted() {
            var app = this;
            axios.get('/api/friend').then(function (response) {
                app.vkFriends      = response.data.vkFriends;
                app.userFriendIds  = response.data.userFriendIds;
                app.updated_at     = response.data.updated_at;
                app.todayStatistic = response.data.todayStatistic;
                app.todayFriendsStatistic = response.data.todayFriendsStatistic;
            });
        },
        methods: {
            goToFriend(id) {
                this.$router.push('/statistic/' + id + '/visits');
            }
        }
    }
</script>