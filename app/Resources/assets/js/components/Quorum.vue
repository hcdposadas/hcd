<template>
    <div>{{ quorum }}</div>
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
