<template>
    <div ref="modalExpediente" class="modal fade bs-example-modal-lg" tabindex="-1" role="dialog">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    {{ expediente.expediente }}
                </div>
                <div class="modal-body">
                    <div class="cuerpo" v-html="expediente.texto"></div>
                    <div class="anexos">
                        <template v-for="anexo in expediente.anexos">
                            <img class="img-responsive" :src="baseUrl + '/uploads/expedientes/anexos/' + anexo.id">
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
        mounted() {
            window.$(this.$refs.modalExpediente).modal('toggle').on('hidden.bs.modal', () => {
                this.$emit('hidden')
            })
        }
    }
</script>