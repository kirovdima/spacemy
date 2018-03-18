<template>
    <div class="table-responsive">
    <table class="table table-striped sortable">
        <tbody>
        <tr v-for="friend in vkFriends" :key="friend.id" v-on:click="goToFriend(friend.id)" style="cursor:pointer">
            <td><img :src="friend.photo_50"></td>
            <td>
                {{ friend.first_name }} {{ friend.last_name }}
            </td>
            <td>
                <div v-if="userFriendIds.includes(friend.id)" class="imgContainer"><img :src="'images/spy-male.png'"></div>
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
            goToFriend(id) {
                this.$router.push('/statistic/' + id + '/show');
            }
        }
    }
</script>