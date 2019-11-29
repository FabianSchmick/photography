import 'select2';
import 'select2/dist/js/i18n/de';
import 'summernote/dist/summernote-bs4';
import 'summernote/lang/summernote-de-DE';

// Initializes select2 fields
export function initSelect2() {
    $.fn.select2.defaults.set("theme", "bootstrap4");
    $.fn.select2.defaults.set("language", $("html").attr("lang"));
    $.fn.select2.defaults.set("placeholder", "");

    $(".select2").select2();

    $(".select2.add").select2({
        tags: true
    });

    $(".clear-select2").on("click", function () {
        $(this).siblings("select.select2").val(null).trigger("change");
    });
}

// Initializes summernote wysiwyg editor
export function initWysiwyg() {
    var lang = "";

    if ($("html").attr("lang") === "de") {
        lang = "de-DE";
    }

    $(".wysiwyg").summernote({
        height: 100,
        lang: lang,
        disableDragAndDrop: true,
        linkTargetBlank: false,
        dialogsInBody: true,
        toolbar: [
            // [groupName, [list of button]]
            ["misc", ["undo", "redo"]],
            ["style", ["clear", "bold"]],
            ["insert", ["link"]],
            ["para", ["ul"]],
            ["misc", ["codeview", "help"]]
        ],
        onCreateLink: function(linkUrl) {
            return /^([A-Za-z][A-Za-z0-9+-.]*\:[\/\/]?|\/)/.test(linkUrl) ?
                linkUrl : "http://" + linkUrl;
        }
    });
}
