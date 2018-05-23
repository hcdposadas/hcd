<template>
    <dl>
        <dt>
            <!--data-id-expediente="{{ dictamen.expediente.dictamenes[0].id }}"-->
            <a @click="mostrarDictamen">EXPTE. Nº {{ dictamen.expediente.expediente }}</a>
        </dt>
        <dd v-html="dictamen.expediente.extractoDictamen"></dd>
    </dl>
</template>
<script>
    export default {
        props: {
            dictamen: {
                required: true
            }
        },
        methods: {
            mostrarDictamen() {
                window.$('#modal-dictamen .modal-header').html(this.dictamen.expediente.expediente);

                window.$('#modal-dictamen .modal-body').html(this.dictamen.dictamen.texto);

                if (this.dictamen.dictamen.firmantes){
                    window.$('#modal-dictamen .modal-body').append('<h4>Firmantes</h4>');
                    this.dictamen.dictamen.firmantes.forEach(function(firmante) {
                        window.$('#modal-dictamen .modal-body').append(firmante + '<br>');
                    });
                }

                window.$('#modal-dictamen').modal('toggle')
            }
        }
    }
</script>