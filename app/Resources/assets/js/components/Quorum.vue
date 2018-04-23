<template>
    <span>{{ quorum }}</span>
</template>

<script>
    export default {
        computed: {
            quorum() {
                return this.$root.$data.quorum
            }
        },
        mounted() {
            //
        }
    }
</script>
