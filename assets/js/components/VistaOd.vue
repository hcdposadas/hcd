<template>
    <div>
        <div v-if="cargando">
            <div class="text-center text-muted"><i class="fa fa-fw fa-spin fa-circle-o-notch"></i> Cargando...</div>
        </div>
        <div v-else>
            <div class="od">
                <div v-if="sesion.ordenDelDia" v-html="sesion.ordenDelDia"></div>
                <div v-else>
                    <template v-for="dictamenes in sesion.dictamenes">
                        <vista-od-grupo-dictamenes :dictamenes="dictamenes"></vista-od-grupo-dictamenes>
                    </template>
                </div>
            </div>
        </div>
    </div>
</template>
<script>
    import VistaOdGrupoDictamenes from './VistaOdGrupoDictamenes'
    export default {
        components: {
            'vista-od-grupo-dictamenes': VistaOdGrupoDictamenes
        },
        props: {
            idSesion: {
                default: 15
            }
        },
        data() {
            return {
                cargando: true,
                sesion: null,
            }
        },
        mounted() {
            axios.get(baseUrl + 'sesion/consultar-sesion/' + this.idSesion).then(({data}) => {
                this.cargando = false
                this.sesion = data.sesion
            })
        }
    }
</script>