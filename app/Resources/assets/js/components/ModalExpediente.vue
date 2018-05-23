<template>
    <div ref="modalExpediente" class="modal fade bs-example-modal-lg" tabindex="-1" role="dialog">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    {{ expediente.expediente }}
                    <div v-if="expediente.fechaPresentacion" class="pull-right">Presentado el {{ fecha(expediente.fechaPresentacion) }}</div>
                </div>
                <div class="modal-body">
                    <div class="cuerpo" v-html="expediente.texto"></div>
                    <div v-if="expediente.giros && expediente.giros.length" class="giros">
                        <hr>
                        <h3>Giros</h3>
                        <div v-html="expediente.textoDelGiro"></div>
                    </div>
                    <div v-if="expediente.autor" class="autor">
                        <hr>
                        <h3>Iniciadores</h3>
                        <div>
                            <ul>
                                <li v-for="iniciador in expediente.iniciadores">
                                    {{ iniciador.cargo }} {{ iniciador.nombre }} <span v-if="iniciador.esAutor">(Autor)</span>
                                </li>
                            </ul>
                        </div>
                    </div>
                    <div v-if="expediente.anexos && expediente.anexos.length" class="anexos">
                        <hr>
                        <h3>Anexos</h3>
                        <template v-for="anexo in expediente.anexos">
                            <img class="img-responsive" :src="imgSrc(anexo)">
                            <span>{{ anexo.descripcion }}</span>
                        </template>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
<script>
    export default {
        props: {
            expediente: {
                required: true
            }
        },
        methods: {
            fecha(fecha) {
                return fecha.substring(0, 10)
                            .split('-')
                            .reverse()
                            .join('/')
            },
            imgSrc(anexo) {
                return assetPath + 'expedientes/anexos/' + anexo.anexo
            }
        },
        mounted() {
            window.$(this.$refs.modalExpediente).modal('toggle').on('hidden.bs.modal', () => {
                this.$emit('hidden')
            })
        }
    }
</script>