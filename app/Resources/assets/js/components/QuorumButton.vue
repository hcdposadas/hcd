<style scoped>
    .dropdown {
        display: inline-block;
    }
</style>
<template>
    <div class="dropdown">
        <button class="btn btn-primary dropdown-toggle" type="button" data-toggle="dropdown">
            Presentes <span class="badge"><quorum-count></quorum-count></span>
        </button>
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
</template>

<script>
    import QuorumCount from './QuorumCount'
    export default {
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
        mounted() {
            axios.get(baseUrl + 'sesion/concejales').then(({data}) => {
                this.concejales = data.concejales
            })
        }
    }
</script>
