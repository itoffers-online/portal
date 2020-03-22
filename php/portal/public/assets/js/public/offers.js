const jquery = require('jquery');
Trix = require('trix');
window.jQuery = jquery;
window.$ = jquery;
const saveMyForm = require('savemyform.jquery');

require('bootstrap');

Trix.config.toolbar.getDefaultHTML = function() {
    return `
        <div class="trix-button-row">
          <span class="trix-button-group trix-button-group--text-tools" data-trix-button-group="text-tools">
            <button type="button" class="trix-button trix-button--icon trix-button--icon-bold" data-trix-attribute="bold" data-trix-key="b" title="#{lang.bold}" tabindex="-1">#{lang.bold}</button>
            <button type="button" class="trix-button trix-button--icon trix-button--icon-italic" data-trix-attribute="italic" data-trix-key="i" title="#{lang.italic}" tabindex="-1">#{lang.italic}</button>
            <button type="button" class="trix-button trix-button--icon trix-button--icon-bullet-list" data-trix-attribute="bullet" title="#{lang.bullets}" tabindex="-1">#{lang.bullets}</button>
            <button type="button" class="trix-button trix-button--icon trix-button--icon-number-list" data-trix-attribute="number" title="#{lang.numbers}" tabindex="-1">#{lang.numbers}</button>
          </span>
          <span class="trix-button-group-spacer"></span>
          <span class="trix-button-group trix-button-group--history-tools" data-trix-button-group="history-tools">
            <button type="button" class="trix-button trix-button--icon trix-button--icon-undo" data-trix-action="undo" data-trix-key="z" title="#{lang.undo}" tabindex="-1">#{lang.undo}</button>
            <button type="button" class="trix-button trix-button--icon trix-button--icon-redo" data-trix-action="redo" data-trix-key="shift+z" title="#{lang.redo}" tabindex="-1">#{lang.redo}</button>
          </span>
        </div>
        <div class="trix-dialogs" data-trix-dialogs>
          <div class="trix-dialog trix-dialog--link" data-trix-dialog="href" data-trix-dialog-attribute="href">
            <div class="trix-dialog__link-fields">
              <input type="url" name="href" class="trix-input trix-input--dialog" placeholder="#{lang.urlPlaceholder}" aria-label="#{lang.url}" required data-trix-input>
              <div class="trix-button-group">
                <input type="button" class="trix-button trix-button--dialog" value="#{lang.link}" data-trix-method="setAttribute">
                <input type="button" class="trix-button trix-button--dialog" value="#{lang.unlink}" data-trix-method="removeAttribute">
              </div>
            </div>
          </div>
        </div>    
    `;
};

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

    document.querySelectorAll("trix-editor").forEach(
        function(editorElement) {
            editorElement.addEventListener("trix-change", () => {
                $('#' + editorElement.getAttribute('input')).change();
            });
        }
    );

    // This keeps the state also for textareas where trix editor is used.
    $.saveMyForm.addCallback({
        match:function(element){
            if (element.tagName === 'TEXTAREA') {
                return true;
            }
        },
        loadElement: function(element, plugin){
            plugin.loadElement(element);

            if (document.querySelector('trix-editor[input="' + element.id + '"]')) {
                document.querySelector('trix-editor[input="' + element.id + '"]').editor.setSelectedRange([0, element.value.length]);
                document.querySelector('trix-editor[input="' + element.id + '"]').editor.insertHTML(element.value);
            }
        }
    });
});