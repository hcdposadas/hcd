<style scoped lang="scss">
    $color-hcd-verde: #17a867;
    .cuerpo {
        background-color: #fff;
        color: #333;
        /*position: fixed;*/
        top: 0;
        bottom: 0;
        left: 0;
        right: 0;
        font-family: Arial
    }

    .img-logo {
        position: absolute;
        width: 10%;
        z-index: 1;
    }

    .titulo {
        text-align: center;
        font-size: 2em;
        height: 10rem;
        /*height: 2em;*/
        /*padding-top: 10px;*/
        padding: 10px;
        background-color: $color-hcd-verde;
        color: #fff;
    }

    .panel-presentes-asistencia {
        font-size: 6em;
    }

    .panel-presentes-asistencia-numero {
        font-size: 17rem;
        font-weight: bold;
    }

    .panel-quorum {
        font-size: 4em;
        /*text-align: center;*/
        /*clear: both;*/
        /*background-color: #333;*/
        /*background-color: #17a867;*/
        color: #fff;
        /*padding: 20px;*/
        padding: 1rem;
    }

    .no-quorum {
        background-color: #d44950;
    }

    .si-quorum {
        background-color: #5cb85c;
    }

    .panel-votacion-sesion {
        font-size: 4em;
        text-align: center;
        clear: both;
        background-color: $color-hcd-verde;
        color: #fff;
        padding: 20px;
    }

    .panel-resultado-texto {
        font-size: 6em;
        clear: both;
        /*background-color: #333;*/
        color: #fff;
        padding: 20px;
    }

    .texto-tipo-mayoria {
        font-size: 3em;
        padding: 20px;
        font-weight: bold;
        color: #31708f;

    }

    .texto-mocion {
        font-size: 5em;
        padding: 20px;
        font-weight: bold;
    }

    .texto-texto-mocion {
        font-size: 2em;
    }

    .texto-resultado {
        font-size: 4em;
        padding: 10px
    }

    .bold{
        font-weight: bold;
    }

    .texto-resultado-sesion {
        font-size: 4em;
        text-align: center;
        clear: both;
        background-color: $color-hcd-verde;
        /*background-color: #333;*/
        color: #fff;
        padding: 20px;
    }
</style>
<template>
    <div class="container-body">
        <div class="row">
            <div class="col-lg-12">
                <div class="titulo">
                    <img class="img-responsive img-logo" :src="logoSrc">
                    <h1>
                        Honorable Concejo Deliberante de la Ciudad de Posadas
                    </h1>
                </div>
            </div>
        </div>

        <div v-if="panel==='presentes'" class="row">
            <div class="col-lg-12">
                <div class="row panel-presentes-asistencia">
                    <div class="col-lg-6 text-center">
                        <div>Presentes</div>
                        <div class="panel-presentes-asistencia-numero">{{ quorum.presentes }}</div>
                    </div>
                    <div class="col-lg-6 text-center">
                        <div>Ausentes</div>
                        <div class="panel-presentes-asistencia-numero">{{ quorum.ausentes }}</div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-lg-12 panel-quorum text-center" :class="[quorum.hayQuorum ? 'si-quorum' : 'no-quorum']">
                        <span v-if="quorum.hayQuorum">Hay Quorum</span>
                        <span v-else>No Hay Quorum</span>
                    </div>
                </div>
            </div>
        </div>

        <div v-if="panel==='votacion'" style="text-align: center;">
            <div class="panel-votacion-sesion">
                {{ sesion }}
            </div>
            <div style="font-size: 5em; padding: 20px; font-weight: bold;">{{ mocion }}</div>
            <div style="font-size: 2em;">{{ textoMocion }}</div>
            <hr>
            <div style="font-size: 3em;">{{ tipoMayoria }}</div>
            <hr>
            <div style="text-align: center; font-size: 3em; width: 50%; float: left">
                <div>Presentes</div>
                <div style="font-size: 3em;">{{ quorum.presentes }}</div>
            </div>
            <div style="text-align: center; font-size: 3em; width: 50%; float: right">
                <div>Ausentes</div>
                <div style="font-size: 3em;">{{ quorum.ausentes }}</div>
            </div>
            <div v-if="tiempo" style="clear: both; background-color: #333; color: #fff; font-size: 2em; padding: 20px;">
                Restan {{ tiempo ? duracion - tiempo : '' }} segundos
            </div>
        </div>
        <div v-if="panel==='resultados'" class="row text-center">
            <div class="col-lg-12">
                <div class="row texto-mocion">{{ mocion }}</div>

                <div class="col-lg-12 panel-quorum text-center" :class="[resultados.aprobado ? 'si-quorum' : 'no-quorum']">
                    {{ resultados.aprobado ? 'Aprobado' : 'No Aprobado' }}
                </div>
                <!--<hr>-->
                <div class="row">
                    <div v-if="resultados.aprobado == 'Aprobado'" class="texto-resultado">Afirmativos: {{ resultados.afirmativos }}</div>
                    <span v-if="resultados.aprobado == 'Aprobado'" class="texto-resultado bold" v-for="concejal in resultados.votaronPositivo">{{ concejal }}.-</span>
                    <div v-if="resultados.aprobado == 'No Aprobado'" class="texto-resultado">Negativos: {{ resultados.negativos }}</div>
                    <span v-if="resultados.aprobado == 'No Aprobado'" class="texto-resultado bold" v-for="concejal in resultados.votaronNegativo">{{ concejal }}.-</span>
                    <div class="texto-resultado">Abstenciones: {{ resultados.abstenciones }}</div>
                    <span class="texto-resultado bold" v-for="concejal in resultados.seAbstuvieron">{{ concejal }}.-</span>
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
            'logoSrc'
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
