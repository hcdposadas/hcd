<style scoped>
    .container {
        margin-top: 15rem;
    }
</style>
<template>
    <div>
        <content-placeholders v-show="loading" class="container">
            <content-placeholders-text :lines="10"/>
        </content-placeholders>
        <template v-show="!loading">
            <div data-spy="scroll" data-target=".navbar" data-offset="50">

                <nav class="navbar navbar-fixed-top">
                    <div class="">
                        <div class="row">
                            <div class="col-md-12 text-center">

                                <button type="button" class="btn btn-app btn-primary-hcd"
                                        @click="verAe">
                                    <i class="fa fa-file"></i>
                                    BAE
                                </button>
                                <button type="button" class="btn btn-app btn-primary-hcd"
                                        @click="consultarExpte">
                                    <i class="fa fa-search" aria-hidden="true"></i>
                                    Consultar Expte
                                </button>
                                <button type="button" class="btn btn-app btn-primary-hcd"
                                        @click="verOd">
                                    <i class="fa fa-file-o" aria-hidden="true"></i>
                                    OD
                                </button>
                                <button type="button" class="btn btn-app btn-primary-hcd"
                                        @click="verCartaOrganica">
                                    <i class="fa fa-file-o" aria-hidden="true"></i>
                                    Carga Orgánica
                                </button>
                                <button type="button" class="btn btn-app btn-primary-hcd"
                                        @click="verActas">
                                    <i class="fa fa-file-text-o" aria-hidden="true"></i>
                                    Sesiones
                                </button>

                                <a class="btn btn-danger btn-lg pull-right" :href="pathLogout"> Salir
                                    <span class="glyphicon glyphicon-log-out"></span>
                                </a>
                            </div>
                        </div>
                    </div>
                </nav>

                <div class="container">
                    <div class="row">
                        <div class="col-md-12">
                            <b>
                                <template v-if="fecha">
                                    {{ fecha.date | moment("LL") }}
                                </template>
                                - {{ titulo }}
                            </b>
                        </div>
                        <div class="col-md-12">

                        </div>
                    </div>
                    <div class="row m-t-5">
                        <div class="col-md-12">
                            <vista-bae v-if="idSesion && showBae" :id-sesion="idSesion"></vista-bae>
                            <vista-od v-if="idSesion && showOd" :id-sesion="idSesion"></vista-od>

                            <!--<div v-html="texto" v-show="showTexto" data-spy="scroll"-->
                            <!--data-target="#navbar-example2" data-offset="0">-->
                            <!--</div>-->

                            <div class="embed-responsive embed-responsive-16by9" v-show="showCartaOrganica">
                                <embed class="embed-responsive-item"
                                       :src="pathCartaOrganica">
                            </div>

                            <consultar-expediente v-show="showConsultarExpte"></consultar-expediente>

                            <consultar-sesiones v-show="showActas"></consultar-sesiones>

                            <panel-votacion></panel-votacion>
                        </div>
                    </div>
                </div>

            </div>
        </template>
    </div>
</template>

<script>
    export default {
        props: [
            'pathLogout',
            'pathCartaOrganica',
            'concejal'
        ],
        data() {
            return {
                loading: true,
                ae: null,
                od: null,
                showTexto: false,
                showBae: false,
                showOd: false,
                showConsultarExpte: false,
                showCartaOrganica: false,
                showActas: false,
                titulo: null,
                fecha: null,
                texto: null,
                idSesion: null
            }
        },
        methods: {
            verAe() {
                this.showConsultarExpte = false
                this.showCartaOrganica = false
                this.showTexto = false
                this.showBae = true
                this.showOd = false
                this.texto = this.ae;
                this.showActas = false
            },
            verOd() {
                this.showConsultarExpte = false
                this.showCartaOrganica = false
                this.showTexto = false
                this.showBae = false
                this.showOd = true
                this.texto = this.od;
                this.showActas = false
            },
            verCartaOrganica() {
                this.showCartaOrganica = true
                this.showConsultarExpte = false
                this.showTexto = false
                this.showBae = false
                this.showOd = false
                this.showActas = false
            },
            consultarExpte() {
                this.showConsultarExpte = true
                this.showCartaOrganica = false
                this.showTexto = false
                this.showBae = false
                this.showOd = false
                this.showActas = false
            },
            verActas() {
                this.showConsultarExpte = false
                this.showCartaOrganica = false
                this.showTexto = false
                this.showBae = false
                this.showOd = false
                this.showActas = true
            }
        },
        mounted() {
            console.log('Concejal ready.')

            axios.get(window.baseUrl + 'sesion/ultima-sesion').then(response => {
                console.log(response);

                let data = response.data;

                // this.od = data.s_ordenDelDia;
                // this.ae = data.s_asuntosEntrados;
                this.titulo = data.s_titulo;
                this.fecha = data.s_fecha;
                this.idSesion = data.s_id;

                this.loading = false;

            });


        }
    }
</script>
