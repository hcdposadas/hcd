<template>
    <dl>
        <dt><strong>EXPTE. Nº {{ dictamen.expediente.expediente }}</strong></dt>
        <dd v-html="dictamen.expediente.extractoDictamen"></dd>
    </dl>
</template>
<script>
    export default {
        props: {
            dictamen: {
                required: true
            }
        }
    }
</script>