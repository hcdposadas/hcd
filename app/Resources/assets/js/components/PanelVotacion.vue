<style>
    #cabecera {
        background-color: pink !important;
    }
</style>
<template>
    <div style="z-index: 3000; position: absolute; left: 15px; right: 15px; top: 15px;"
         :style="{ display: display ? 'block' : 'none' }">
        <div class="box">
            <div class="box-body" style="height: 465px;">
                <h4 style="background-color: #f7f7f7; font-size: 18px; text-transform: uppercase; padding: 5px;">{{
                    sesion }}</h4>
                <h1>{{ mocion }}</h1>
                <p>{{ textoMocion }}</p>

                <div v-if="duracion">
                    <div class="clearfix">
                        <span class="pull-left">{{ tipoMayoria }}</span>
                        <small class="pull-right">Restan {{ duracion - tiempo }} segundo{{ (duracion - tiempo) ===
                            1 ? '': 's' }}
                        </small>
                    </div>
                    <div class="progress xs">
                        <div class="progress-bar" :class="{
                            'progress-bar-success': porcentaje < 60,
                            'progress-bar-warning': porcentaje >= 60 && porcentaje <= 80,
                            'progress-bar-danger': porcentaje > 80
                        }" :style="{width: porcentaje + '%'}"></div>
                    </div>
                </div>

                <hr>
                <div>
                    <div v-if="error" class="callout callout-danger">
                        <p><b>Atención:</b> El voto no se registró.</p>
                        <p>{{ error }}</p>
                    </div>
                    <div v-if="success" class="callout callout-success">
                        <p>!El voto se registró correctamente!</p>
                    </div>

                    <div v-if="!yaVoto">
                        <button class="btn btn-app"
                                style="height: 150px; font-size: 3em; width: 45%; color: #fff; background-color: #00a65a; border-color: #008d4c;"
                                @click="votarSi">SI
                        </button>
                        <button class="btn btn-app btn-danger pull-right"
                                style="height: 150px; font-size: 3em; width: 45%; color: #fff; background-color: #dd4b39; border-color: #d73925;"
                                @click="votarNo">NO
                        </button>
                    </div>
                    <div v-else>
                        <div class="callout callout-info">
                            <p>Su voto fue emitido</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
    const io = require('socket.io-client')

    export default {
        data() {
            return {
                display: false,
                tipoMayoria: '',
                sesion: '',
                fecha: '',
                mocion: '',
                textoMocion: '',
                tiempo: 0,
                duracion: 0,
                yaVoto: false,
                voto: null,
                error: null,
                success: null
            }
        },
        computed: {
            porcentaje() {
                if (!this.duracion) {
                    return 0
                }
                return this.tiempo * 100 / this.duracion
            }
        },
        watch: {
            display: function (val) {
                $('.main-header .navbar').css('margin-left', '230px');
                if (val) {
                    $('body').addClass('layout-top-nav');
                    $('.main-sidebar').hide();
                    $('.sidebar-toggle').hide();
                } else {
                    $('body').removeClass('layout-top-nav');
                    $('.main-sidebar').show();
                    $('.sidebar-toggle').show();

                    // this.mocion no se reinicia, porque se usa para saber si es la misma
                    // moción que se extiende o es una nueva
                    // this.mocion = ''
                    this.duracion = 0
                    this.tiempo = 0
                    this.textoMocion = ''
                    this.sesion = ''
                    this.fecha = ''
                    this.error = null
                    this.success = null
                }
            }
        },
        mounted() {
            let socket = io('http://' + document.location.host + ':3000', {
                transports: ['websocket'],
                upgrade: true,
                query: {concejalId: window.user.id}
            });

            socket.on('connect', function () {
                // console.log('socket.connect')
            });
            socket.on('disconnect', function () {
                // console.log('socket.disconnect')
            });
            socket.on('message', function (msg) {
                // console.log('socket.message', msg);
                switch (msg.type) {
                    case 'votacion.abierta':
                        if (this.mocion !== msg.data.mocion) {
                            this.yaVoto = false
                            this.success = null
                            this.error = null
                        }
                        this.mocion = msg.data.mocion;
                        this.display = true;
                        this.sesion = msg.data.sesion;
                        this.tipoMayoria = msg.data.tipoMayoria;
                        this.textoMocion = msg.data.textoMocion;
                        this.duracion = msg.data.duracion;
                        this.tiempo = msg.data.tiempo;
                        break;
                    case 'votacion.tick':
                        this.tiempo = msg.data.tiempo;
                        break;
                    case 'votacion.cerrada':
                        this.display = false;
                        break;
                }
            }.bind(this));
        },
        methods: {
            votarSi() {
                this.voto = 'si'
                this.enviarVoto()
            },
            votarNo() {
                this.voto = 'no'
                this.enviarVoto()
            },
            enviarVoto() {
                this.error = null

                this.yaVoto = true
                axios.post(window.baseUrl + 'votar', {
                    voto: this.voto,
                    user: window.user.id,
                }).then(function (response) {
                    if (!this.display) {
                        return
                    }

                    let data = response.data;

                    if (data.status === 'error') {
                        this.error = data.message
                        this.voto = null
                        this.confirmarVoto = false
                    } else {
                        this.success = true
                    }
                }.bind(this)).catch(function (error) {
                    this.error = error
                    this.yaVoto = false
                }.bind(this))
            }
        }
    }
</script>
