/**
 * Created by matias on 15/4/17.
 */
$(document).ready(function () {
    $(".data-table").DataTable({
        "paging": false,
        "autoWidth": true,
        "info": false,
        "scrollX": true,
        "order": [],
        "language": {
            "search": "Buscar:",
            "zeroRecords": "Sin resultados"
        }
    });
    inicializarPlugins();
});

function inicializarPlugins() {
    $('.datepicker').datepicker({
        format: "dd/mm/yyyy",
        language: "es",
        autoclose: true
    });
}