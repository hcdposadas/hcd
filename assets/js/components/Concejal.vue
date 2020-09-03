<style scoped>
    /*.container {*/
    /*    margin-top: 15rem;*/
    /*}*/
</style>
<template>
    <div>
        <!--        <content-placeholders v-show="loading" class="container">-->
        <!--            <content-placeholders-text :lines="10"/>-->
        <!--        </content-placeholders>-->
        <template v-show="!loading">
            <div data-spy="scroll" data-target=".navbar" data-offset="50">

                <nav class="main-header navbar navbar-expand-md navbar-dark navbar-primary">
                    <div class="container-fluid">
                        <div class="collapse navbar-collapse order-3" id="navbarCollapse">
                            <!-- Left navbar links -->
                            <ul class="navbar-nav">

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
                                    <i class="far fa-file" aria-hidden="true"></i>
                                    OD
                                </button>
                                <button type="button" class="btn btn-app btn-primary-hcd"
                                        @click="verCartaOrganica">
                                    <i class="far fa-file" aria-hidden="true"></i>
                                    Carga Orgánica
                                </button>
                                <button type="button" class="btn btn-app btn-primary-hcd"
                                        @click="verActas">
                                    <i class="far fa-file-alt" aria-hidden="true"></i>
                                    Sesiones
                                </button>
                            </ul>
                            <ul class="order-1 order-md-3 navbar-nav navbar-no-expand ml-auto">
                                <li class="nav-item">
                                    <a class="nav-link" :href="pathLogout"> Salir
                                        <span class="glyphicon glyphicon-log-out"></span>
                                    </a>
                                </li>
                            </ul>
                        </div>
                    </div>
                </nav>

                <div class="container">

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

            axios.get(baseUrl + 'sesion/ultima-sesion').then(response => {
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
