<template>
    <dl class="dl-horizontal">
        <dt>
            <strong><a @click="mostrarDictamen">EXPTE. Nº {{ dictamen.expediente.expediente }}</a></strong>
        </dt>
        <dd v-html="dictamen.extracto"></dd>
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
            fecha(fecha) {
                return fecha.substring(0, 10)
                    .split('-')
                    .reverse()
                    .join('/')
            },
            mostrarDictamen() {
                let header = this.dictamen.expediente.expediente

                if (this.dictamen.expediente.fechaPresentacion) {
                    header += '<div class="pull-right">Presentado el ' +this.fecha(this.dictamen.expediente.fechaPresentacion) + '</div>'
                }
                window.$('#modal-dictamen .modal-header').html(header);

                window.$('#modal-dictamen .modal-body').html(this.dictamen.dictamen.texto);

                if (this.dictamen.dictamen.firmantes){
                    window.$('#modal-dictamen .modal-body').append('<h4>Firmantes</h4>');
                    this.dictamen.dictamen.firmantes.forEach(function(firmante) {
                        firmante = firmante.toLocaleLowerCase().split(' ').map(
                            (w) => {
                                return w.charAt(0).toLocaleUpperCase() + w.substr(1)
                            }).join(' ')

                        window.$('#modal-dictamen .modal-body').append(firmante + '<br>');
                    });
                }

                window.$('#modal-dictamen').modal('toggle')
            }
        }
    }
</script>