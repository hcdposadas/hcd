<template>
    <div>
        <div :style="{display: display?'block':'none'}" style="position: fixed; top: 0; bottom: 0; left: 0; right: 0; z-index: 2000; background-color: #fff;">
            <div><h1>{{ sesion }}</h1></div>
            <div><h2>Moción Nº{{ mocion }}</h2></div>
            <div v-if="tipoMayoria" ><h3>Se require {{ tipoMayoria }}</h3></div>
            <div><b>{{ tiempo }}</b></div>
            <div>
                <button>SÍ</button>
            </div>
            <div>
                <button>NO</button>
            </div>
        </div>
    </div>
</template>

<script>
    const io = require('socket.io-client')

    export default {
        data() {
            return {
                display: true,
                socket: null,
                tipoMayoria: null,
                sesion: null,
                mocion: null,
                tiempo: null
            }
        },
        mounted() {
            this.socket = io('http://localhost:3000', {
                transports: ['websocket'],
                upgrade: true,
                query: { uid: window.user.id }
            });
            this.socket.on('connect', function () {
                console.log('socket.connect')
            });
            this.socket.on('disconnect', function () {
                console.log('socket.disconnect')
            });
            this.socket.on('message', function (msg) {
                console.log('socket.message', msg);
                switch (msg.type) {
                    case 'votacion.abierta':
                    case 'votacion.tick':
                        this.display = true;
                        this.sesion = msg.data.sesion;
                        this.tipoMayoria = msg.data.tipoMayoria;
                        this.mocion = msg.data.mocion;
                        this.tiempo = msg.data.tiempo || msg.data.duracion;
                        break;
                    case 'votacion.cerrada':
                        this.display = false;
                        break;
                }
            }.bind(this));
        }
    }
</script>
