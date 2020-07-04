<script>
    export default {
        computed: {
            quorum() {
                return this.$root.$data.quorum
            }
        },
        mounted() {
            //
        },
        render(h) {
            return h('span', this.quorum)
        }
    }
</script>
