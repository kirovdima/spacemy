<template>
    <div>
        <template v-for="list_change in friends_list_change">
            <div v-for="user in list_change.delete" class="m-1 m-sm-2 m-md-4 clearfix">
                <div class="col-9 col-md-6 float-left border border-danger rounded px-3 py-2" style="background-color: #fff7f7!important;">
                    <div class="float-left"><img :src="user.photo_50"></div>
                    <div class="float-left mx-1 mx-md-3 text-left">{{ user.first_name }}<br>{{ user.last_name }}</div>
                    <div class="float-right small text-muted"><em>Удален(-а):<br>{{ list_change.date }}</em></div>
                </div>
            </div>
            <div v-for="user in list_change.add" class="m-1 m-sm-2 m-md-4 clearfix">
                <div class="col-9 col-md-6 float-right border border-success rounded px-3 py-2" style="background-color: #f7fff8!important;">
                    <div class="float-right"><img :src="user.photo_50"></div>
                    <div class="float-right mx-1 mx-md-3 text-right">{{ user.first_name }}<br>{{ user.last_name }}</div>
                    <div class="float-left small text-muted"><em>Добавлен(-а):<br>{{ list_change.date }}</em></div>
                </div>
            </div>
        </template>
        <div v-if="friends_list_change.length == 0" class="text-center">
            <span v-if="wait"><img width="36px" :src="'/images/loader.gif'"></span>
            <span v-else>У пользователя пока нет изменений в списке друзей</span>
        </div>
    </div>
</template>

<script>
    export default {

        data: function () {
            return {
                friends_list_change: [],
                wait: true,
            }
        },

        props: [
            'person_id',
        ],

        mounted () {
            var app = this;
            axios.get('/api/statistic/' + app.person_id + '/friend').then(function (response) {
                app.friends_list_change = response.data.friends_list_change;
                app.wait = false;
            })
        },

    }
</script>