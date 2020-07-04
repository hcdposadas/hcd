<style scoped>
    #miniDisplay {
        position: fixed;
        left: 30px;
        bottom: 30px;
        width: 320px;
        height: 240px;
        border: 1px solid #ccc;
        border-radius: 4px;
        background-color: #fff;
        z-index: 10;
    }

    .img-logo {
        width: 30px;
        position: absolute;
        left: 5px;
        top: 5px;
    }

    #title {
        background: #17a867;
        color: #fff;
        width: 100%;
        text-align: center;
        font-size: 1.1em;
    }

    .panel-quorum {
        color: #fff;
        padding: 0.5rem;
        position: absolute;
        left: 0;
        right: 0;
        bottom: 0;
    }

    .no-quorum {
        background-color: #d44950;
    }

    .si-quorum {
        background-color: #5cb85c;
    }

    #presentes {
        position: absolute;
        left: 0;
        right: 50%;
        bottom: 40px;
        top: 50px;
    }

    #ausentes {
        position: absolute;
        left: 50%;
        right: 0%;
        bottom: 40px;
        top: 50px;
    }

    #presentes,
    #ausentes {
        text-align: center;
        font-size: 1.2em;
    }

    .panel-presentes-asistencia-numero {
        font-size: 3em;
    }

    .texto-mocion {
        padding-left: 15px;
        padding-top: 10px;
        font-weight: bold;
        padding-right: 15px;
        text-align: center;
    }

    .texto-resultado:not(.text-uppercase) {
        padding-left: 15px;
        padding-right: 15px;
        text-align: center;

    }

    .center {
        text-align: center;
        padding-bottom: 5px;
    }

    .panel-resultado-aprobado-no-aprobado {
        margin-top: 10px;
        margin-bottom: 10px;
        color: #fff;
    }
</style>
<template>
    <div id="miniDisplay">
        <div id="title">{{ appName }}</div>
        <img class="img-logo" :src="logoSrc"/>

        <template v-if="panel === 'presentes'">
            <div id="presentes">
                <div>Presentes</div>
                <div class="panel-presentes-asistencia-numero">{{ quorum.presentes }}</div>
            </div>
            <div id="ausentes">
                <div>Ausentes</div>
                <div class="panel-presentes-asistencia-numero">{{ quorum.ausentes }}</div>
            </div>
            <div class="panel-quorum text-center" :class="[quorum.hayQuorum ? 'si-quorum' : 'no-quorum']">
                <span v-if="quorum.hayQuorum">Hay Quórum</span>
                <span v-else>No Hay Quórum</span>
            </div>
        </template>
        <template v-if="panel === 'votacion'">
            <br><br><br><br><br>
            <div class="center">Votación en curso</div>
        </template>
        <template v-if="panel === 'resultados'">
            <div class="col-lg-12">
                <div class="row texto-mocion">{{ mocion }}</div>

                <div class="col-lg-12 panel-resultado-aprobado-no-aprobado text-center"
                     :class="[resultados.aprobado ? 'si-quorum' : 'no-quorum']">
                    {{ resultados.aprobado ? 'Aprobado' : 'No Aprobado' }}
                </div>
                <!--<hr>-->
                <div class="row">
                    <div class="texto-resultado">Afirmativos: {{ resultados.afirmativos }}
                    </div>
                    <div class="center">
                  <span class="texto-resultado bold text-uppercase"
                        v-for="concejal in resultados.votaronPositivo">{{ concejal }}.-</span>
                    </div>

                    <div class="texto-resultado">Negativos: {{ resultados.negativos }}</div>
                    <div class="center">
                  <span class="texto-resultado bold text-uppercase"
                        v-for="concejal in resultados.votaronNegativo">{{ concejal }}.-</span>
                    </div>

                    <div class="texto-resultado">Abstenciones: {{ resultados.abstenciones }}</div>
                    <div class="center">
                  <span class="texto-resultado bold text-uppercase"
                        v-for="concejal in resultados.seAbstuvieron">{{ concejal }}.-</span>
                    </div>
                </div>
            </div>
        </template>
    </div>
</template>

<script>
    const io = require('socket.io-client')
    export default {
        props: ["logoSrc"],
        data() {
            return {
                quorum: {
                    presentes: "-",
                    ausentes: "-",
                    hayQuorum: null
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
                panel: "presentes",
                appName: appName
            };
        },
        methods: {},
        mounted() {
            let socket = io("http://" + nodeHost + ":3000", {
                transports: ["websocket"],
                upgrade: true
            });
            socket.on(
                "message",
                function (msg) {
                    console.log(msg);
                    switch (msg.type) {
                        case "quorum":
                            this.quorum.presentes = msg.data.quorum;
                            this.quorum.ausentes = msg.data.ausentes;
                            this.quorum.hayQuorum = msg.data.hayQuorum;

                            break;
                        case "votacion.finalizada":
                            this.panel = "presentes";
                            break;
                        case "votacion.abierta":
                            this.mocion = msg.data.mocion;
                            this.display = true;
                            this.sesion = msg.data.sesion;
                            this.tipoMayoria = msg.data.tipoMayoria;
                            this.textoMocion = msg.data.textoMocion;
                            this.duracion = msg.data.duracion;
                            this.tiempo = msg.data.tiempo;

                            this.panel = "votacion";

                            break;
                        case "votacion.tick":
                            this.tiempo = msg.data.tiempo;
                            break;
                        case "votacion.cerrada":
                            break;
                        case "votacion.resultados":
                            this.mocion = msg.data.mocion;
                            this.resultados.afirmativos = msg.data.afirmativos;
                            this.resultados.negativos = msg.data.negativos;
                            this.resultados.abstenciones = msg.data.abstenciones;
                            this.resultados.total = msg.data.total;
                            this.resultados.aprobado = msg.data.aprobado;
                            this.resultados.votaronNegativo = msg.data.votaronNegativo;
                            this.resultados.votaronPositivo = msg.data.votaronPositivo;
                            this.resultados.seAbstuvieron = msg.data.seAbstuvieron;

                            this.panel = "resultados";
                            break;
                    }
                }.bind(this)
            );
        }
    };
</script>
