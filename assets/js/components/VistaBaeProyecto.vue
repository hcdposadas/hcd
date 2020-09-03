<template>
    <div class="proyecto">
        <modal-expediente v-if="expediente" :expediente="expediente" @hidden="modalOcultado"></modal-expediente>
        <p>
            <dl class="dl-horizontal">
                <dt>
                    <strong><a @click="mostrarExpediente">EXPTE. Nº {{ proyecto.expediente.expediente }}</a></strong>
                </dt>
                <dd v-html="proyecto.extracto"></dd>
            </dl>
        </p>
        <p style="text-align: center" v-html="proyecto.expediente.textoDelGiro"></p>
    </div>
</template>
<script>
    export default {
        props: {
            proyecto: {
                required: true
            }
        },
        data() {
            return {
                expediente: null
            }
        },
        methods: {
            mostrarExpediente() {
                this.expediente = this.proyecto.expediente
            },
            modalOcultado() {
                this.expediente = null
            }
        }
    }
</script>