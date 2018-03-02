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
                    <div class="row">
                        <div class="col-md-12 text-center">

                            <button type="button" class="btn btn-primary btn-primary-hcd btn-circle btn-xl"
                                    @click="verAe">
                                <i class="fa fa-file" aria-hidden="true"></i><br>
                                AE
                            </button>
                            <button type="button" class="btn btn-primary btn-primary-hcd btn-circle btn-xl"
                                    @click="consultarExpte">
                                <i class="fa fa-search" aria-hidden="true"></i><br>
                                <span style="font-size: 12px">Consultar Expte</span>
                            </button>
                            <button type="button" class="btn btn-primary btn-primary-hcd btn-circle btn-xl"
                                    @click="verOd">
                                <i class="fa fa-file-o" aria-hidden="true"></i><br>
                                OD
                            </button>
                            <button type="button" class="btn btn-primary btn-primary-hcd btn-circle btn-xl"
                                    @click="verOd">
                                <i class="fa fa-file-o" aria-hidden="true"></i><br>
                                <span style="font-size: 12px">Carga Orgánica</span>
                            </button>
                            <a class="btn btn-danger btn-lg pull-right" :href="pathLogout"> Salir
                                <span class="glyphicon glyphicon-log-out"></span>
                            </a>
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
                    </div>
                    <div class="row m-t-1">
                        <div class="col-md-12">
                            <div v-html="texto" v-show="!showConsultarExpte" data-spy="scroll"
                                 data-target="#navbar-example2" data-offset="0">

                            </div>

                            <consultar-expediente v-if="showConsultarExpte"></consultar-expediente>

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
            'concejal'
        ],
        data() {
            return {
                loading: true,
                ae: null,
                od: null,
                showConsultarExpte: false,
                titulo: null,
                fecha: null,
                texto: null
            }
        },
        methods: {
            verAe() {
                this.showConsultarExpte = false
                this.texto = this.ae;
            },
            verOd() {
                this.showConsultarExpte = false
                this.texto = this.od;
            },
            consultarExpte() {

                this.showConsultarExpte = true

            }
        },
        mounted() {
            console.log('Concejal ready.')

            axios.get(window.baseUrl + 'sesion/ultima-sesion').then(response => {
                console.log(response);

                let data = response.data;

                this.od = data.s_ordenDelDia;
                this.ae = data.s_asuntosEntrados;
                this.titulo = data.s_titulo;
                this.fecha = data.s_fecha;

                this.loading = false;

            });


        }
    }
</script>
