<template>
    <div class="row">

        <div id="modal-acta" class="modal fade bs-example-modal-lg" tabindex="-1" role="dialog"

             aria-labelledby="myLargeModalLabel">
            <div class="modal-dialog modal-lg" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        Acta
                    </div>
                    <div class="modal-body">
                       Acta

                    </div>
                </div>
            </div>
        </div>

        <div id="modal-homenajes" class="modal fade bs-example-modal-lg" tabindex="-1" role="dialog"

             aria-labelledby="myLargeModalLabel">
            <div class="modal-dialog modal-lg" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        Homenajes
                    </div>
                    <div class="modal-body">


                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-12">
            <div class="row">
                <div class="col-md-12">
                    <h3>
                        Consultar Sesiones
                    </h3>
                </div>
            </div>

            <div class="row m-t-1" v-show="sesiones.length > 0">
                <div class="col-md-12">

                    <table class="table">
                        <tbody>
                        <tr>
                            <th>
                                Sesión
                            </th>
                            <th>
                                Acta
                            </th>
                            <th>
                                Homenajes
                            </th>
                        </tr>
                        <tr v-for="sesion in sesiones">
                            <td>
                                {{ sesion.text }}
                            </td>
                            <td>
                                <a href="#" @click="verActa(sesion)">
                                    <i class="fa fa-file"></i>
                                </a>
                            </td>
                            <td>
                                <a href="#" @click="verHomenaje(sesion)">
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
                sesiones: [],
                buscando: false
            }
        },
        methods: {
            verSesiones() {
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

                axios.get(window.baseUrl + 'sesion/consultar-sesiones', params).then(response => {
                    console.log('response expte', response);
                    this.sesiones = response.data;
                    this.buscando = false;
                })
            },
            verActa(sesion){
                console.log(sesion);
                window.$('#modal-acta').modal('toggle')
                window.$('#modal-acta .modal-header').html(sesion.titulo);
                window.$('#modal-acta .modal-body').html(sesion.acta);
            },
            verHomenaje(sesion){
                console.log(sesion);
                window.$('#modal-homenajes').modal('toggle')
                window.$('#modal-homenajes .modal-header').html(sesion.titulo);
                window.$('#modal-homenajes .modal-body').html(sesion.homenajes);
            }
        },
        mounted() {
            console.log('ConsultarSesiones ready.')
            this.verSesiones()
        }
    }
</script>
