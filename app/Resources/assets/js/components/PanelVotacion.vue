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
                        <small class="pull-right">Restan {{ tiempoTotal - tiempo }} segundo{{ (tiempoTotal - tiempo) ===
                            1 ? '': 's' }}
                        </small>
                    </div>
                    <div class="progress xs">
                        <div class="progress-bar" :class="{
                            'progress-bar-success': (100 * tiempo / tiempoTotal) < 60,
                            'progress-bar-warning': (100 * tiempo / tiempoTotal) >= 60 && (100 * tiempo / tiempoTotal) <= 80,
                            'progress-bar-danger': (100 * tiempo / tiempoTotal) > 80
                        }" :style="{width: (100 * tiempo / tiempoTotal) + '%'}"></div>
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


    <!--<div id="contenedor"-->
    <!--style="display: none !important; color: #333; position: fixed; top: 0; bottom: 0; left: 0; right: 0; z-index: 2000; background-color: #fff; border-bottom: 1px solid #000;">-->
    <!--<div id="cabecera"-->
    <!--style="position: absolute; top: 0; left: 0; right: 0; height: 30%">-->
    <!--<div style="background-color: #d73925; color: #fff; padding: 12px 15px 11px; font-family: 'Helvetica Neue', Helvetica,Arial,sans-serif;">-->
    <!--<div id="fecha" class="pull-right" style="font-size: 14px, line-height: 20px; padding-top: 4px;">{{ fecha }}</div>-->
    <!--<div id="sesion" style="font-size: 1.4em;">{{ sesion }}</div>-->
    <!--</div>-->
    <!--<div id="mocion" style="font-size: 3em; border: 2px solid #ccc; padding: 10px 12px;">{{ mocion }}</div>-->
    <!--<div id="tiempo" class="pull-right" style="font-size: 1.5em;">{{ tiempo }}</div>-->
    <!--<div id="tipo-mayoria" style="font-size: 1.5em;">{{ tipoMayoria }}</div>-->
    <!--</div>-->
    <!--<div id="botones" style="position: absolute; bottom: 0; left: 0; right: 0; top: 30%;">-->
    <!--<div id="boton-si" style="position: absolute; bottom: 0; left: 0; right: 50%; top: 0; padding: 5%;">-->
    <!--<button class="btn btn-success"-->
    <!--style="position: absolute; top: 5%; left: 5%; right: 5%; bottom: 5%; width: 90%; font-size: 15em;">-->
    <!--SI-->
    <!--</button>-->
    <!--</div>-->
    <!--<div id="boton-no" style="position: absolute; bottom: 0; left: 50%; right: 0; top: 0; padding: 5%;">-->
    <!--<button class="btn btn-danger"-->
    <!--style="position: absolute; top: 5%; left: 5%; right: 5%; bottom: 5%; width: 90%; font-size: 15em;">-->
    <!--NO-->
    <!--</button>-->
    <!--</div>-->
    <!--</div>-->
    <!--</div>-->
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
                tiempoTotal: 100
            }
        },
        watch: {
            display: function (val) {
                $('.main-header .navbar').css('margin-left', '230px');
                if (val) {
                    $('body').addClass('layout-top-nav');
                    $('.main-sidebar').hide();
                    $('.sidebar-toggle').hide();
                    // $('.box:not(.box-debe-permanecer)').hide();
                    // $('.box-debe-permanecer').show();
                } else {
                    $('body').removeClass('layout-top-nav');
                    $('.main-sidebar').show();
                    $('.sidebar-toggle').show();
                    // $('.box:not(.box-debe-permanecer)').show();
                    // $('.box-debe-permanecer').hide();
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

            setInterval(function () {
                this.display = !this.display
                this.display = true
                if (this.tiempo < 100) {
                    this.tiempo += 10;
                }
            }.bind(this), 500)
        }
    }
</script>
