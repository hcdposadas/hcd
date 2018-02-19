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


import PanelVotacion from './components/PanelVotacion.vue';

Vue.component('panel-votacion', PanelVotacion);

const app = new Vue({
    delimiters: ['[[', ']]'],
    el: '#app',
    data: {
    },
    methods: {

    }
});