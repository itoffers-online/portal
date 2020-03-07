const jquery = require('jquery');
window.jQuery = jquery;
window.$ = jquery;
const saveMyForm = require('savemyform.jquery');

require('bootstrap');

$(function () {
    $('[data-toggle="tooltip"]').tooltip();
    $('[data-toggle="popover"]').popover({
        'html' : true,
        trigger: 'click',
        animation: true
    }).on('inserted.bs.popover', function(e) {
        e.preventDefault();
    });
    $('[data-href]').on('click', function() {
        window.location = $(this).attr('data-href');
    });
});