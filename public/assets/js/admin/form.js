import 'select2';
import 'select2/dist/js/i18n/de';
import 'summernote/dist/summernote-bs4';
import 'summernote/lang/summernote-de-DE';

$(document).on('change', '.custom-file-input', function() {
    let $this = $(this);
    $this.parent().find('label').text($this.val().replace('C:\\fakepath\\', ''));
});

/**
 * Initializes select2 fields
 */
export function initSelect2() {
    $.fn.select2.defaults.set('theme', 'bootstrap4');
    $.fn.select2.defaults.set('language', $('html').attr('lang'));
    $.fn.select2.defaults.set('placeholder', '');

    $('.select2').each((index, el) => {
        let $el = $(el);

        $el.select2({
            allowClear: !$el.prop('required'),
            tags: !!$el.prop('multiple'),
            width: '100%',
            placeholder: $el.find('option[value=""]').length ? $el.find('option[value=""]').text() : ''
        });
    });
}

/**
 * Initializes summernote wysiwyg editor
 */
export function initWysiwyg() {
    let lang = '';

    if ($('html').attr('lang') === 'de') {
        lang = 'de-DE';
    }

    $('.wysiwyg').summernote({
        height: 100,
        lang: lang,
        disableDragAndDrop: true,
        linkTargetBlank: false,
        dialogsInBody: true,
        toolbar: [
            // [groupName, [list of button]]
            ['misc', ['undo', 'redo']],
            ['style', ['clear', 'bold']],
            ['insert', ['link']],
            ['para', ['ul']],
            ['misc', ['codeview', 'help']]
        ],
        callbacks: {
            // Clear all formatting of the pasted text (https://github.com/summernote/summernote/issues/1168#issuecomment-236568117)
            onPaste: (e) => {
                let bufferText = ((e.originalEvent || e).clipboardData || window.clipboardData).getData('Text');
                e.preventDefault();
                setTimeout(() => {
                    document.execCommand('insertText', false, bufferText);
                }, 10);
            }
        },
        onCreateLink: (linkUrl) => {
            return /^([A-Za-z][A-Za-z0-9+-.]*\:[\/\/]?|\/)/.test(linkUrl) ?
                linkUrl : 'http://' + linkUrl;
        }
    });
}
