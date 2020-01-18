const jquery = require('jquery');
window.jQuery = jquery;
window.$ = jquery;
const saveMyForm = require('savemyform.jquery');

require('bootstrap');

$(function () {
    $('[data-toggle="tooltip"]').tooltip();
});