// We need bootstrap for bootstrap-webpack
require('bootstrap');

import Vue from 'vue';
// const Vue = require('vue');
global.Vue = Vue;

global.axios = require('axios');

global.axios.defaults.headers.common = {
    // 'X-CSRF-TOKEN': '',
    'X-Requested-With': 'XMLHttpRequest'
};


import Example from './components/Example.vue';

Vue.component('example', Example);

const app = new Vue({
    // delimiters: ['[[', ']]'],
    el: '#app',
    data: {
    },
    methods: {

    }
});