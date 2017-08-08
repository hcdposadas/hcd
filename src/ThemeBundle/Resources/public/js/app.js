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
    $('.reset').click(function () {
        var form = $(this).parents().find('form');

        form.find('input, textarea, input:not([type="submit"])').removeAttr('value');
        form.find('input, textarea, input:not([type="submit"])').val('');
        form.find('input:radio, input:checkbox').removeAttr('checked').removeAttr('selected');

        form.find('select option').removeAttr('selected').find('option:first').attr('selected', 'selected');
    });

    $('.select2entity').each(function (index) {
        $(this).select2({
            allowClear: true,
            ajax: {
                url: $(this).data('rpath'),
                dataType: $(this).data('data-type'),
                delay: 250,
                data: function (params) {
                    return {
                        q: params.term,
                        page_limit: $(this).data('page-limit')
                    };
                },
                processResults: function (data) {
                    var myResults = [];
                    $.each(data, function (index, item) {
                        myResults.push({
                            'id': item.id,
                            'text': item.text
                        });
                    });
                    return {
                        results: myResults
                    };
                }
            },
        });
        var val = $(this).data('value');
        if (val.id) {
            var $option = $("<option selected></option>").val(val.id).text(val.text);
            $(this).append($option).trigger('change');
        }
    });

    inicializarPlugins();
});

function inicializarPlugins(elem) {
    $('.datepicker').datepicker({
        format: "dd/mm/yyyy",
        language: "es",
        autoclose: true
    });

    if (typeof elem !== "undefined") {
        
        var $elem = elem.find('select');
        // console.log('$elem', $elem);
        if ($elem.hasClass('select2entity')) {
            $elem.select2({
                allowClear: true,
                ajax: {
                    url: $elem.data('rpath'),
                    dataType: $elem.data('data-type'),
                    delay: 250,
                    data: function (params) {
                        return {
                            q: params.term,
                            page_limit: $elem.data('page-limit')
                        };
                    },
                    processResults: function (data) {
                        var myResults = [];
                        $.each(data, function (index, item) {
                            myResults.push({
                                'id': item.id,
                                'text': item.text
                            });
                        });
                        return {
                            results: myResults
                        };
                    }
                },
            });

            var val = $elem.data('value');

            if (val.id) {
                var $option = $("<option selected></option>").val(val.id).text(val.text);
                $elem.append($option).trigger('change');
            }
        }

        if ($elem.hasClass('select2')) {

            $elem.select2();
        }

    }

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