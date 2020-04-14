<style scoped>
    .top-dropdown, .dropdown {
        display: inline-block;
    }
</style>
<template>
    <div class="top-dropdown">
        <div class="dropdown">
            <div class="btn-group" role="group">
                <button class="btn btn-primary dropdown-toggle" type="button" data-toggle="dropdown">
                    Presentes <span class="badge"><quorum-count></quorum-count></span>
                </button>
            </div>
            <ul class="dropdown-menu">
                <li v-if="concejales.length === 0">
                    <span class="text-muted"><i class="fa fa-fw fa-spin fa-circle-o-notch"></i>Cargando...</span>
                </li>
                <template v-for="concejal in concejales">
                    <li>
                        <a href="#" v-if="presentes.includes(concejal.id)">
                            <span class="text-success"><i class="fa fa-fw fa-check"></i></span>
                            {{ concejal.nombre }}
                        </a>
                        <a href="#" v-else class="text-gray">
                            <span><i class="fa fa-fw fa-times"></i></span>
                            {{ concejal.nombre }}
                        </a>
                    </li>
                </template>
            </ul>
        </div>
        <span class="btn btn-default" @click="mostrarPresentes" title="Mostrar pantalla de presentes">
            <i class="fa fa-arrow-right" style="font-size: 0.5em; vertical-align: middle;"></i>
            <i class="fa fa-desktop" style="font-size: 0.8em;"></i>
        </span>
    </div>
</template>

<script>
    import QuorumCount from './QuorumCount'

    export default {
        props: [
            'firewall'
        ],
        components: {
            'quorum-count': QuorumCount
        },
        computed: {
            presentes() {
                return this.$root.$data.presentes
            }
        },
        data() {
            return {
                concejales: []
            }
        },
        methods: {
            mostrarPresentes() {
                axios.get(window.baseUrl + 'sesion/mocion/mostrarPresentes')
            }
        },
        mounted() {
            let url = baseUrl + 'sesion/concejales'

            if (this.firewall == 'admin') {
                url = baseUrl + 'concejales'
            }

            axios.get(url).then(({data}) => {
                this.concejales = data.concejales
            })
        }
    }
</script>
