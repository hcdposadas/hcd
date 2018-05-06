export const modalAlert = (msg) => {
    window.$('#modal-alert .modal-body').html(msg);
    window.$('#modal-alert').modal('toggle');
}
window.modalAlert = modalAlert;

export const modalConfirm = (titulo, body, okButonHref) => {
    window.$('#modal-confirm .modal-body').html(body);
    window.$('#modal-confirm #myModalLabel').html(titulo);
    window.$('#modal-confirm #modal-btn-ok').attr('href', okButonHref);
    window.$('#modal-confirm').modal('toggle');
}
window.modalConfirm = modalConfirm;

export const bootstrapCollectionBorrarItem = (item) => {
    window.$(item).parent().parent().remove();
}
window.bootstrapCollectionBorrarItem = bootstrapCollectionBorrarItem;

// export const inicializarPlugins = (elem) => {
//     $('.datepicker').datepicker({
//         format: "dd/mm/yyyy",
//         language: "es",
//         autoclose: true
//     });
//
//     if (typeof elem !== "undefined") {
//
//         var $elem = elem.find('select');
//         // console.log('$elem', $elem);
//         if ($elem.hasClass('select2entity')) {
//             $elem.select2({
//                 allowClear: true,
//                 ajax: {
//                     url: $elem.data('rpath'),
//                     dataType: $elem.data('data-type'),
//                     delay: 250,
//                     data: function (params) {
//                         return {
//                             q: params.term,
//                             page_limit: $elem.data('page-limit')
//                         };
//                     },
//                     processResults: function (data) {
//                         var myResults = [];
//                         $.each(data, function (index, item) {
//                             myResults.push({
//                                 'id': item.id,
//                                 'text': item.text
//                             });
//                         });
//                         return {
//                             results: myResults
//                         };
//                     }
//                 },
//             });
//
//             var val = $elem.data('value');
//
//             if (val.id) {
//                 var $option = window.$("<option selected></option>").val(val.id).text(val.text);
//                 $elem.append($option).trigger('change');
//             }
//         }
//
//         if ($elem.hasClass('select2')) {
//
//             $elem.select2();
//         }
//
//     }
//
// }
//
// window.inicializarPlugins = inicializarPlugins;