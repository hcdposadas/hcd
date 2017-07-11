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
    $('.select2').select2();
    inicializarPlugins();
});

function inicializarPlugins() {
    $('.datepicker').datepicker({
        format: "dd/mm/yyyy",
        language: "es",
        autoclose: true
    });
    $('.bootstrapcollection .select2').select2();
}

function modalAlert(msg) {
    $('#modal-alert .modal-body').html(msg);
    $('#modal-alert').modal('toggle');
}

/**
 * Abre un modal para confirmar
 *
 * @param titulo
 * @param body
 * @param okButonHref
 */
function modalConfirm(titulo, body, okButonHref) {
    $('#modal-alert .modal-body').html(body);
    $('#modal-alert #myModalLabel').html(titulo);
    $('#modal-alert #modal-btn-ok').attr('href', okButonHref);
    $('#modal-alert').modal('toggle');
}

function bootstrapCollectionBorrarItem(item) {
    $(item).parent().parent().remove();
}