const jquery = require('jquery');

require('bootstrap');

window.$ = jquery;

$(function () {
    $('[data-toggle="tooltip"]').tooltip();
});