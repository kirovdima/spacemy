<template>
    <div class="table-responsive">
    <table class="table table-striped">
        <tbody>
        <tr v-for="friend in vkFriends" :key="friend.id">
            <td><img :src="friend.photo_50"></td>
            <td>
                {{ friend.first_name }} {{ friend.last_name }}
            </td>
            <td>
                <router-link class="btn btn-outline-success" role="button"
                             v-if="userFriendIds.includes(friend.id)"
                             :to="{ name: 'person_statistic', params: { person_id: friend.id }}">
                    Статистика
                </router-link>
                <!--a v-if="userFriendIds.includes(friend.id)" v-on:click="deleteFriend(friend.id)" class="btn btn-outline-danger" role="button">Удалить</a-->
                <a v-else="" v-on:click="addFriend(friend.id)" class="btn btn-outline-primary" role="button">Добавить</a>
            </td>
        </tr>
        </tbody>
    </table>
    </div>
</template>

<script>
    export default {
        data: function () {
            return {
                vkFriends:     [],
                userFriendIds: [],
            }
        },
        mounted() {
            var app = this;
            axios.get('/api/friend').then(function (response) {
                app.vkFriends     = response.data.vkFriends.items;
                app.userFriendIds = response.data.userFriendIds;
            });
        },
        methods: {
            addFriend(id) {
                var app = this;
                axios.post('/api/friend/' + id).then(function (response) {
                    app.userFriendIds.push(id);
                })
            },
            deleteFriend(id) {
                var app = this;
                axios.delete('/api/friend/' + id).then(function (response) {
                    app.userFriendIds.splice(app.userFriendIds.indexOf(id), 1);
                });
            }
        }
    }
</script>