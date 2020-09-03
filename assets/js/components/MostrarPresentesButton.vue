<template>
    <!--    <div>-->

    <!--        <span class="btn btn-default" @click="mostrarPresentes" v-if="firewall !== 'admin'" title="Mostrar pantalla de presentes">-->
    <!--            <i class="fa fa-arrow-right" style="font-size: 0.5em; vertical-align: middle;"></i>-->
    <!--            <i class="fa fa-desktop" style="font-size: 0.8em;"></i>-->
    <!--        </span>-->
    <!--    </div>-->

    <li class="nav-item dropdown">
        <a class="nav-link" data-toggle="dropdown" href="#" @click="mostrarPresentes" v-if="firewall !== 'admin'"
           title="Mostrar pantalla de presentes">
            <i class="fas fa-arrow-right"></i>
            <i class="fas fa-desktop"></i>
        </a>

    </li>
</template>

<script>

    export default {
        name: "MostrarPresentesButton",
        props: [
            'firewall'
        ],
        computed: {},
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

        }
    }
</script>

<style scoped>

</style>