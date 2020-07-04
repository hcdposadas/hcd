/*
 * Welcome to your app's main JavaScript file!
 *
 * We recommend including the built version of this JavaScript file
 * (and its CSS file) in your base layout (base.html.twig).
 */

// any CSS you require will output into a single css file (app.css in this case)
// require('../css/app.css');

// Need jQuery? Install it with "yarn add jquery", then uncomment to require it.
// const $ = require('jquery');

// console.log('Hello Webpack Encore! Edit me in assets/js/app.js');

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

export const inicializarPlugins = (item) => {

    if (item) {
        item.find('.select2').select2(
            {language: "es"}
        );
        item.find('.select2entity').select2entity()
    }

}
window.inicializarPlugins = inicializarPlugins;

// window.$.widget.bridge('uibutton', window.$.ui.button)