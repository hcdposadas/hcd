<template>
    <div v-if="Object.keys(dictamenes.dictamenes).length > 0">
        <h3 style="text-align: center; margin-top: 5%"><u>{{ dictamenes.titulo }}</u></h3>
        <div class="cuerpo">
            <p>
            <template v-for="dictamen in dictamenes.dictamenes">
                <vista-od-dictamen :dictamen="dictamen"></vista-od-dictamen>
            </template>
            </p>
        </div>
    </div>
</template>
<script>
    import VistaOdDictamen from './VistaOdDictamen'
    export default {
        components: {
            'vista-od-dictamen': VistaOdDictamen
        },
        props: {
            dictamenes: {
                required: true
            }
        }
    }
</script>