<template>
    <div v-if="proyectos.length > 0">
        <h3 style="text-align: center; margin-top: 5%"><u>{{ titulo }}</u></h3>
        <div class="cuerpo">
            <template v-for="proyecto in proyectos">
                <vista-bae-proyecto :proyecto="proyecto"></vista-bae-proyecto>
            </template>
        </div>
    </div>
</template>
<script>
    import VistaBaeProyecto from './VistaBaeProyecto'
    export default {
        components: {
            'vista-bae-proyecto': VistaBaeProyecto
        },
        props: {
            proyectos: {
                required: true
            },
            tipo: {
                required: true
            }
        },
        computed: {
            titulo() {
                return this.tipo
            }
        }
    }
</script>