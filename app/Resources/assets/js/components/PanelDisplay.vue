<template>
    <div style="background-color: #fff; color: #333; position: fixed; top: 0; bottom: 0; left: 0; right: 0; font-family: Arial">
        <div style="text-align: center; font-size: 2em; height: 2em; padding-top: 10px;">Honorable Concejo Deliberante de la Ciudad de Posadas</div>
        <div v-if="panel==='presentes'" style="width: 100%;">
            <div style="text-align: center; font-size: 6em; width: 50%; float: left">
                <div>Presentes</div>
                <div style="font-size: 6em;">{{ quorum.presentes }}</div>
            </div>
            <div style="text-align: center; font-size: 6em; width: 50%; float: right">
                <div>Ausentes</div>
                <div style="font-size: 6em;">{{ quorum.ausentes }}</div>
            </div>
            <div style="font-size: 4em; text-align: center; clear: both; background-color: #333; color: #fff; padding: 20px;">
                <div v-if="quorum.hayQuorum" style="">Hay Quorum</div>
                <div v-else style="">No Hay Quorum</div>
            </div>
        </div>

        <div v-if="panel==='votacion'" style="text-align: center;">
            <div style="font-size: 4em; text-align: center; clear: both; background-color: #333; color: #fff; padding: 20px;">{{ sesion }}</div>
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
            <div v-if="tiempo" style="clear: both; background-color: #333; color: #fff; font-size: 2em; padding: 20px;">Restan {{ tiempo ? duracion - tiempo : '' }} segundos</div>
        </div>
        <div v-if="panel==='resultados'" style="text-align: center;">
            <div style="font-size: 4em; text-align: center; clear: both; background-color: #333; color: #fff; padding: 20px;">{{ sesion }}</div>
            <div style="font-size: 5em; padding: 20px; font-weight: bold;">{{ mocion }}</div>
            <div style="font-size: 2em;">{{ textoMocion }}</div>
            <hr>
            <div style="font-size: 3em;">{{ tipoMayoria }}</div>
            <hr>
            <div>
                <div style="font-size: 3em; padding: 10px">Afirmativos: {{ resultados.afirmativos }}</div>
                <div style="font-size: 3em; padding: 10px">Negativos: {{ resultados.negativos }}</div>
                <div style="font-size: 3em; padding: 10px">Abstenciones: {{ resultados.abstenciones }}</div>
            </div>
            <div style="font-size: 6em; clear: both; background-color: #333; color: #fff; padding: 20px;">
                {{ resultados.aprobado ? 'Aprobado' : 'No Aprobado' }}
            </div>

        </div>
    </div>
</template>

<script>
    const io = require('socket.io-client')

    export default {
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
                    aprobado: null
                },
                panel: 'presentes'
            }
        },
        mounted() {
            let socket = io('http://' + document.location.host + ':3000', {
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

                        this.panel = 'resultados';
                        break;

                }
            }.bind(this))
        }
    }
</script>
