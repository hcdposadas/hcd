<template>
    <div class="row">
        <modal-expediente v-if="expediente" :expediente="expediente" @hidden="modalOcultado"></modal-expediente>
        <div class="col-md-12">
            <div class="row">
                <div class="col-md-12">
                    <h3>
                        Consultar Expedientes
                    </h3>
                </div>
            </div>
            <div class="row">
                <div class="col-md-4">
                    <div class="form-group has-feedback">
                        <input class="form-control" type="text" id="numero" name="numero"
                               @keyup.enter="buscarExpediente"
                               v-model="numero"
                               placeholder="Nro Expediente (99999 C 2018)"/>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group has-feedback">
                        <input class="form-control" type="text" id="tema" name="tema"
                               @keyup.enter="buscarExpediente"
                               v-model="tema"
                               placeholder="Tema..."/>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group has-feedback">
                        <input class="form-control" type="text" id="contenido" name="contenido"
                               @keyup.enter="buscarExpediente"
                               v-model="contenido"
                               placeholder="Contenido..."/>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-12">
                    <button type="button" class="btn btn-primary" @click="buscarExpediente" v-show="buscando" disabled>
                        <i class="fa fa-spinner fa-spin"></i> Buscar Expediente
                    </button>
                    <button type="button" class="btn btn-primary" @click="buscarExpediente" v-show="!buscando">
                        Buscar Expediente
                    </button>
                </div>
            </div>

            <div class="row mt-1" v-show="expedientes.length > 0">
                <div class="col-md-12">

                    <table class="table">
                        <tbody>
                        <tr>
                            <th>
                                Expte
                            </th>
                            <th>
                                Tema
                            </th>
                            <th>
                                Archivos
                            </th>
                        </tr>
                        <tr v-for="expediente in expedientes">
                            <td>{{ expediente.expediente }}</td>
                            <td>{{ expediente.extracto }}</td>
                            <td>
                                <a href="#" @click="verExpte(expediente)">
                                    <i class="fa fa-file"></i>
                                </a>
                            </td>
                        </tr>
                        </tbody>
                    </table>

                </div>
            </div>
        </div>


    </div>
</template>

<script>
    export default {
        data() {
            return {
                numero: null,
                tema: null,
                contenido: null,
                expedientes: [],
                expediente: null,
                buscando: false,
                anexos: [],
            }
        },
        methods: {
            buscarExpediente() {
                this.buscando = true;
                console.log(this.numero);

                let params = {
                    params: {
                        data: {
                            expediente: this.numero,
                            tema: this.tema,
                            texto: this.contenido,
                        }
                    }
                }
                // console.log('baseUrl', baseUrl)
                // console.log('window.baseUrl', window.baseUrl)
                axios.get(baseUrl + 'sesion/buscar-expediente', params).then(response => {
                    console.log('response expte', response);
                    this.expedientes = response.data;
                    this.buscando = false;
                })
            },
            verExpte(expediente) {
                this.expediente = expediente
                // window.$('#modal-expte .modal-header').html(expediente.expediente);
                // // this.anexos = expediente.anexos;
                // window.$('#modal-expte .modal-body').html(expediente.texto);
                //
                // window.$('#modal-expte').modal('toggle')
            },
            // mostrarExpediente() {
            //     this.expediente = this.proyecto.expediente
            // },
            modalOcultado() {
                this.expediente = null
            }
        },
        mounted() {
            console.log('ConsultarExpediente ready.')
        }
    }
</script>
