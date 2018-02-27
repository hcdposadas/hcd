<template>
    <div class="row">

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
                               placeholder="Nro Expediente..."/>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group has-feedback">
                        <input class="form-control" type="text" id="anio" name="anio"
                               @keyup.enter="buscarExpediente"
                               v-model="anio"
                               placeholder="Año..."/>
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
                    <button type="button" class="btn btn-primary" @click="buscarExpediente">
                        Buscar Expediente
                    </button>
                </div>
            </div>

            <div class="row m-t-1" v-show="expedientes.length > 0">
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
                            <td>
                                {{ expediente.expediente }}-{{ expediente.letra }}-{{ expediente.anio }}
                            </td>
                            <td>
                                {{ expediente.extracto }}
                            </td>
                            <td>
                                <a href="#">
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
                anio: null,
                contenido: null,
                expedientes: []
            }
        },
        methods: {
            buscarExpediente() {
                console.log(this.numero);

                let params = {
                    params: {
                        data: {
                            expediente: this.numero,
                            anio: this.anio,
                            texto: this.contenido,
                        }
                    }
                }

                axios.get(window.baseUrl + 'sesion/buscar-expediente', params).then(response => {
                    console.log('response expte', response);
                    this.expedientes = response.data;
                })
            }
        },
        mounted() {
            console.log('ConsultarExpediente ready.')
        }
    }
</script>
