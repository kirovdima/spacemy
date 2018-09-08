<template>
    <ul class="nav nav-pills mt-3 mb-3">
        <li class="nav-item">
            <router-link class="nav-link"
                         :to="{ path: '/' }"
                         v-bind:class="{'active': this.$route.path == '/'}">
                Возможности
            </router-link>
        </li>
        <li class="nav-item">
            <router-link class="nav-link"
                         :to="{ path: '/demo/visits' }"
                         v-bind:class="{'active': this.$route.path == '/demo/visits' || this.$route.path == '/demo/friends'}">
                Пример статистики
            </router-link>
        </li>
        <li class="nav-item">
            <router-link class="nav-link"
                         :to="{ path: '/about' }"
                         v-bind:class="{'active': this.$route.path == '/about'}">
                Как это работает?
            </router-link>
        </li>
    </ul>
</template>