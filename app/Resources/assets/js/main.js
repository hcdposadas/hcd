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

const io = require('socket.io-client')
window.socket = io('http://' + document.location.host + ':3000', {
    transports: ['websocket'],
    upgrade: true,
    query: {concejalId: window.user.id}
});

import PanelVotacion from './components/PanelVotacion';
import Quorm from './components/Quorum';

Vue.component('panel-votacion', PanelVotacion);
Vue.component('quorum', Quorm);

window.app = new Vue({
    delimiters: ['[[', ']]'],
    el: '#app',
    data: {
        quorum: 0
    },
    methods: {},
    mounted: function () {
        socket.on('message', function (message) {
            switch (message.type) {
                case 'quorum':
                    this.quorum = message.data.quorum
                    break;
            }
        }.bind(this))
    }
});

