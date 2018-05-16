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
            У пользователя пока нет изменений в списке друзей
        </div>
        <!--<div class="row mt-5">-->
            <!--<div class="col text-center">Количество друзей пользователя: {{ first_friends_count }}</div>-->
        <!--</div>-->
    </div>
</template>

<script>
    export default {

        data: function () {
            return {
                first_friends_count: null,
                friends_list_change: null,
            }
        },

        mounted () {
            var app = this;
            axios.get('/api/statistic/' + app.$route.params.person_id + '/friend').then(function (response) {
                app.first_friends_count = response.data.first_friends_count;
                app.friends_list_change = response.data.friends_list_change;
            })
        },

    }
</script>