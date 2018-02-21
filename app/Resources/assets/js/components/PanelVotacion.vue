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
                <h4 style="background-color: #f7f7f7; font-size: 18px; text-transform: uppercase; padding: 5px;">Sesión
                    Ordinaria Nº 32</h4>
                <h1>Moción Nº4</h1>
                <p>Lorem ipsum dolor sit amet, consectetur adipisicing elit. Cumque debitis error molestias porro quasi
                    reprehenderit. A aspernatur at corporis dignissimos hic in maiores minima pariatur rem suscipit?
                    Autem recusandae, saepe.</p>

                <div>
                    <div class="clearfix">
                        <span class="pull-left"></span>
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
                <button class="btn btn-app"
                        style="height: 150px; font-size: 3em; width: 45%; color: #fff; background-color: #00a65a; border-color: #008d4c;">
                    SI
                </button>
                <button class="btn btn-app btn-danger pull-right"
                        style="height: 150px; font-size: 3em; width: 45%; color: #fff; background-color: #dd4b39; border-color: #d73925;">
                    NO
                </button>
            </div>
        </div>
    </div>
</template>

<script>
    export default {
        data() {
            return {
                display: false,
                tipoMayoria: 'Mayoría simple',
                sesion: '3ª Sesión - Ordinaria',
                fecha: '20/01/2019',
                mocion: 'Moción 4',
                tiempo: 0,
                duracion: 100
            }
        },
        computed: {
            porcentaje() {
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
                }
            }
        },
        mounted() {
            socket.on('connect', function () {
                console.log('socket.connect')
            });
            socket.on('disconnect', function () {
                console.log('socket.disconnect')
            });
            socket.on('message', function (msg) {
                console.log('socket.message', msg);
                switch (msg.type) {
                    case 'votacion.abierta':
                        this.display = true;
                        this.sesion = msg.data.sesion;
                        this.tipoMayoria = msg.data.tipoMayoria;
                        this.mocion = msg.data.mocion;
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
        }
    }
</script>
