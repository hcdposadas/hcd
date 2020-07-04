<template>
    <div>
        <div v-if="cargando">
            <div class="text-center text-muted"><i class="fa fa-fw fa-spin fa-circle-o-notch"></i> Cargando...</div>
        </div>
        <div v-else>
            <div class="ae">
                <div v-if="sesion.asuntosEntrados" v-html="sesion.asuntosEntrados"></div>
                <div v-else>
                    <template v-for="proyectos in sesion.proyectos">
                        <vista-bae-grupo-proyectos :proyectos="proyectos"></vista-bae-grupo-proyectos>
                    </template>
                </div>
            </div>
        </div>
    </div>
</template>
<script>
    import VistaBaeGrupoProyectos from './VistaBaeGrupoProyectos'
    export default {
        components: {
            'vista-bae-grupo-proyectos': VistaBaeGrupoProyectos
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