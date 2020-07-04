<style scoped lang="scss">
    .panel-presentes-asistencia h1 {
        font-size: 15em;
        margin-top: 3%;
    }

    .panel-presentes-asistencia h2 {
        font-size: 7em;
    }

    .texto-resultado-concejal {
        font-size: 7em;
    }

    .panel-votacion-sesion-presentes-ausentes h2 {
        font-size: 7em;
    }

    .panel-votacion-sesion-presentes-ausentes h3 {
        font-size: 5em;
    }

    .panel-votacion-sesion-tiempo {
        clear: both;

        font-size: 3em;
        padding: 20px;
    }
</style>

<template>
    <div class="content-wrapper">
        <div class="content">
            <div class="container-fluid">
                <div v-if="panel==='presentes'" class="row">
                    <div class="col-lg-12">
                        <div class="row panel-presentes-asistencia">
                            <div class="col-lg-6 text-center">
                                <h2>Presentes</h2>
                                <h1>{{ quorum.presentes }}</h1>
                            </div>
                            <div class="col-lg-6 text-center">
                                <h2>Ausentes</h2>
                                <h1>{{ quorum.ausentes }}</h1>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-12">
                                <div class="alert text-center"
                                     :class="[quorum.hayQuorum ? 'alert-success' : 'alert-danger']">
                                    <h1>
                                        <span v-if="quorum.hayQuorum">Hay Quórum</span>
                                        <span v-else>No Hay Quórum</span>
                                    </h1>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div v-if="panel==='votacion'" class="text-center">
                    <h1 class="panel-votacion-sesion">
                        {{ sesion }}
                    </h1>
                    <h2 style="font-size: 5em; padding: 20px; font-weight: bold;">
                        {{ mocion }}
                    </h2>
                    <div style="font-size: 3em;">{{ textoMocion }} {{ tipoMayoria }}</div>
                    <hr>
                    <!--<div style="text-align: center; font-size: 3em; width: 50%; float: left">-->
                    <div class="row">
                        <div class="col-lg-6 panel-votacion-sesion-presentes-ausentes">
                            <h3>Presentes</h3>
                            <h2>{{ quorum.presentes }}</h2>
                        </div>

                        <div class="col-lg-6 panel-votacion-sesion-presentes-ausentes">
                            <!--<div style="text-align: center; font-size: 3em; width: 50%; float: right">-->
                            <h3>Ausentes</h3>
                            <h2>{{ quorum.ausentes }}</h2>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-lg-12">
                            <h2 v-if="tiempo" class="panel-votacion-sesion-tiempo bg-black">
                                Restan {{ tiempo ? duracion - tiempo : '' }} segundos
                            </h2>
                        </div>

                    </div>

                </div>
                <div v-if="panel==='resultados'" class="row text-center m-t-5">
                    <div class="col-lg-12">
                        <h1>{{ mocion }}</h1>

                        <div class="row">
                            <div class="col-12">
                                <div class="alert text-center"
                                     :class="[quorum.aprobado ? 'alert-success' : 'alert-danger']">
                                    <h1>
                                        <span>{{ resultados.aprobado ? 'Aprobado' : 'No Aprobado' }}</span>
                                    </h1>
                                </div>
                            </div>
                        </div>
                        <!--<hr>-->
                        <div class="row text-center">
                            <div class="col-12">
                                <h1 class="texto-resultado">Afirmativos: {{ resultados.afirmativos }}
                                </h1>

                                <div class="row">
                                    <div class="col-12">
                                    <span class="texto-resultado-concejal bold text-uppercase"
                                          v-for="concejal in resultados.votaronPositivo">{{ concejal }}.-</span>
                                    </div>
                                </div>
                                <h1 class="texto-resultado">Negativos: {{ resultados.negativos }}</h1>
                                <div class="row">
                                    <div class="col-12">
                                    <span class="texto-resultado-concejal bold text-uppercase"
                                          v-for="concejal in resultados.votaronNegativo">{{ concejal }}.-</span>
                                    </div>
                                </div>

                                <h1 class="texto-resultado">Abstenciones: {{ resultados.abstenciones }}</h1>
                                <div class="row">
                                    <div class="col-12">
                                    <span class="texto-resultado-concejal bold text-uppercase"
                                          v-for="concejal in resultados.seAbstuvieron">{{ concejal }}.-</span>
                                    </div>
                                </div>
                            </div>
                        </div>


                    </div>

                </div>
            </div>
        </div>
    </div>
</template>

<script>
    const io = require('socket.io-client')
    // TODO aprobado en grande y primero, sacar tipo de mayoria, y numero de aprobados
    //     lo que gana no mostrar nombres porcentaje
    //     aprobados: total de votos,
    //   abstuvo: total, detalle
    export default {
        props: [
            'logoSrc',
            'ciudadName'
        ],
        data() {
            return {
                quorum: {
                    presentes: null,
                    ausentes: null,
                    hayQuorum: null,
                },
                mocion: null,
                display: null,
                sesion: null,
                tipoMayoria: null,
                textoMocion: null,
                duracion: null,
                tiempo: null,
                resultados: {
                    afirmativos: null,
                    negativos: null,
                    abstenciones: null,
                    total: null,
                    aprobado: null,
                    votaronNegativo: [],
                    votaronPositivo: [],
                    seAbstuvieron: []

                },
                panel: 'presentes'
            }
        },
        mounted() {
            let socket = io('http://' + nodeHost + ':3000', {
                transports: ['websocket'],
                upgrade: true
            });
            socket.on('message', function (msg) {
                console.log(msg)
                switch (msg.type) {
                    case 'quorum':
                        this.quorum.presentes = msg.data.quorum
                        this.quorum.ausentes = msg.data.ausentes
                        this.quorum.hayQuorum = msg.data.hayQuorum

                        break;
                    case 'votacion.finalizada':
                        this.panel = 'presentes'
                        break;
                    case 'votacion.abierta':
                        this.mocion = msg.data.mocion
                        this.display = true
                        this.sesion = msg.data.sesion
                        this.tipoMayoria = msg.data.tipoMayoria
                        this.textoMocion = msg.data.textoMocion
                        this.duracion = msg.data.duracion
                        this.tiempo = msg.data.tiempo

                        this.panel = 'votacion'

                        break;
                    case 'votacion.tick':
                        this.tiempo = msg.data.tiempo;
                        break;
                    case 'votacion.cerrada':
                        break;
                    case 'votacion.resultados':
                        this.mocion = msg.data.mocion
                        this.resultados.afirmativos = msg.data.afirmativos
                        this.resultados.negativos = msg.data.negativos
                        this.resultados.abstenciones = msg.data.abstenciones
                        this.resultados.total = msg.data.total
                        this.resultados.aprobado = msg.data.aprobado
                        this.resultados.votaronNegativo = msg.data.votaronNegativo
                        this.resultados.votaronPositivo = msg.data.votaronPositivo
                        this.resultados.seAbstuvieron = msg.data.seAbstuvieron

                        this.panel = 'resultados';
                        break;

                }
            }.bind(this))
        }
    }
</script>
