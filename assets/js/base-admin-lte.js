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
});