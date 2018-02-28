// We need bootstrap for bootstrap-webpack
// require('bootstrap');
const $ = require('jquery');

// create global $ and jQuery variables
global.$ = global.jQuery = $;

require('bootstrap-sass');

import Vue from 'vue';
// const Vue = require('vue');
global.Vue = Vue;

global.axios = require('axios');

global.axios.defaults.headers.common = {
    // 'X-CSRF-TOKEN': '',
    'X-Requested-With': 'XMLHttpRequest'
};

const io = require('socket.io-client')
window.socket = io('http://' + nodeHost + ':3000', {
    transports: ['websocket'],
    upgrade: true
});

import PanelVotacion from './components/PanelVotacion';
import Concejal from './components/Concejal';
import ConsultarExpediente from './components/ConsultarExpediente';
import PanelDisplay from './components/PanelDisplay';
import Quorm from './components/Quorum';

Vue.component('panel-votacion', PanelVotacion);
Vue.component('panel-concejal', Concejal);
Vue.component('consultar-expediente', ConsultarExpediente);
Vue.component('panel-display', PanelDisplay);
Vue.component('quorum', Quorm);

const moment = require('moment')
require('moment/locale/es')

Vue.use(require('vue-moment'), {
    moment
})

import VueContentPlaceholders from 'vue-content-placeholders'

Vue.use(VueContentPlaceholders)

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

