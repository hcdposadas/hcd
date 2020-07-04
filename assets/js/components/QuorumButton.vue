<style scoped>
    .dropdown {
        display: inline-block;
    }
</style>
<template>

    <li class="nav-item dropdown">
        <a class="nav-link" data-toggle="dropdown" href="#">
            Presentes <span class="badge"><quorum-count class="badge badge-warning navbar-badge"></quorum-count></span>
        </a>
        <div class="dropdown-menu dropdown-menu-lg dropdown-menu-right">
            <span v-if="concejales.length === 0" class="dropdown-header">Cargando... </span>
            <span v-if="concejales.length > 0" class="dropdown-header">Asistencia</span>
            <div class="dropdown-divider"></div>
            <template v-for="concejal in concejales">
                <a href="#" v-if="presentes.includes(concejal.id)" class="dropdown-item text-success">
                    <i class="fas fa-check mr-2"></i> {{ concejal.nombre }}
                </a>
                <a href="#" v-else class="dropdown-item text-gray">
                    <i class="fas fa-times mr-2"></i> {{ concejal.nombre }}
                </a>
                <div class="dropdown-divider"></div>
            </template>
        </div>
    </li>
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
                axios.get(baseUrl + 'sesion/mocion/mostrarPresentes')
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
