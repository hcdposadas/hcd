// try {
//     window.$ = window.jQuery = require('jquery');
//
//     require('bootstrap');
// } catch (e) {}

global.$ = global.jQuery = $;
// require('bootstrap');
require('bootstrap/dist/js/bootstrap.bundle.js');

require('select2')
require('select2/dist/js/i18n/es.js')
// require('jquery-ui-dist/jquery-ui.min.js')
// require('daterangepicker')
require('moment')
require('overlayscrollbars')
// require('fastclick')
require('admin-lte')

$(document).ready(function() {
    // $('[data-toggle="popover"]').popover();
    $('[data-toggle="tooltip"]').tooltip()
    $('.select2').select2(
        { language: "es"}
    );

    $('.reset').click(function (){
        var formNameInputName = $(this).attr('name');
        var formName = formNameInputName.substr(formNameInputName, formNameInputName.indexOf("["))

        var form = $(`form[name="${formName}"]`);
        
        form.find('input, textarea, input:not([type="submit"])').removeAttr('value');
        form.find('input, textarea, input:not([type="submit"])').val('');
        form.find('input:radio, input:checkbox').removeAttr('checked').removeAttr('selected');

        form.find('select option').removeAttr('selected').find('option:first').attr('selected', 'selected');
    })
});