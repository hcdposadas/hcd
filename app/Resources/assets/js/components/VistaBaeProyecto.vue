<template>
    <div class="proyecto">
        <p>
            <dl>
                <dt><strong>EXPTE. Nº {{ proyecto.expediente.expediente }}</strong></dt>
                <dd v-html="proyecto.expediente.extractoTemario"></dd>
            </dl>
        </p>
        <p style="text-align: center" v-html="proyecto.expediente.textoDelGiro"></p>
    </div>
</template>
<script>
    export default {
        props: {
            proyecto: {
                required: true
            }
        }
    }
</script>