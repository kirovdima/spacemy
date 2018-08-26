
/**
 * First we will load all of this project's JavaScript dependencies which
 * includes Vue and other libraries. It is a great starting point when
 * building robust, powerful web applications using Vue and Laravel.
 */

require('./bootstrap');

window.axios = require('axios');
let api_token = document.head.querySelector('meta[name="api-token"]');
if (api_token) {
    window.axios.defaults.headers.common['Authorization'] = 'Bearer ' + api_token.content;
}

window.Vue = require('vue');

import VueRouter from 'vue-router';
window.Vue.use(VueRouter);

import Menu    from './components/MenuComponent.vue';
import Friends from './components/FriendsComponent.vue';
import Footer  from './components/FooterComponent.vue';

import Statistic        from './components/StatisticComponent.vue';
import StatisticVisits  from './components/statistic/StatisticVisitsComponent.vue';
import StatisticFriends from './components/statistic/StatisticFriendsComponent.vue';

const routes = [
    {
        path: '/',
        components: {
            menu:    Menu,
            friends: Friends,
            footer:  Footer,
        },
    },
    {
        path: '/statistic/:person_id',
        name: 'person_statistic',
        components: {
            menu: Menu,
            statistic: Statistic,
            footer: Footer,
        },
        children: [
            {
                path: 'visits',
                name: 'person_visits_statistic',
                components: {
                    statisticVisits: StatisticVisits
                }
            },
            {
                path: 'friends',
                name: 'person_friends_statistic',
                components: {
                    statisticFriends: StatisticFriends
                }
            }
        ]
    },
];

const router = new VueRouter({
    mode: 'history',
    routes: routes
});

/**
 * Next, we will create a fresh Vue application instance and attach it to
 * the page. Then, you may begin adding components to this application
 * or customize the JavaScript scaffolding to fit your unique needs.
 */

const app = new Vue({router}).$mount('#app');
