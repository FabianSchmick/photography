$(document).ready(function() {
    search();
    ajaxPageWrapper();
    initSelect2();
    deleteImageButton();
    initWysiwyg();
});

// Logic for searching the navigation
function search() {
    jQuery("#search").keyup(function () {
        var filter = jQuery(this).val();
        jQuery("ul#side-menu li").each(function () {
            if (jQuery(this).text().search(new RegExp(filter, "i")) < 0) {
                jQuery(this).hide();
            } else {
                jQuery(this).show()
            }
        });
        jQuery('.subNav').addClass('active').find('.nav').collapse('show');

        if (filter === '') {
            jQuery('.subNav').removeClass('active').find('.nav').collapse('hide');
        }
    });
}

// Ajax function for subnavigation
function ajaxPageWrapper() {
    $(".sidebar-nav ul.nav-second-level").on("click", "a", function() {
        // Ajax loader image
        $("#page-wrapper").html('<div id="spinner" class="spinner"><img id="img-spinner" src="/assets/img/layout/ajax-loader.gif" alt="Loading..."/></div>');

        var uri = $(this).attr('href'); // Requested uri

        history.pushState(null, '', uri);

        // Add class active
        $('ul.nav a').removeClass('active');
        $('ul.nav a[href="' + uri + '"]').addClass('active');

        // Get the requested data
        $.get(uri, function(data) {
            var temp = $(data);
            $(temp).find( "script" ).each(function( index ) {
                $(this).remove();
            });
            $("#page-wrapper").replaceWith($(temp).find("#page-wrapper"));
            if($(".nav.navbar-top-links.navbar-right").length){
                $(".nav.navbar-top-links.navbar-right").replaceWith($(temp).find(".nav.navbar-top-links.navbar-right"));
            } else {
                $(temp).find(".nav.navbar-top-links.navbar-right").insertAfter( $("div.navbar-header")  );
            }

            deleteImageButton();
            initSelect2();
            initWysiwyg();
        });

        return false;
    });
}

// When clicked the delete image btn, an input gets the value 1
function deleteImageButton() {
    $('document').ready(function () {
        $('#deleteImage').on('click', function (e) {

            $('input[name="edit[deleteImage]"]').val(1);

            $('#deleteImageAlert').show();

            e.preventDefault();
        })
    });
}

// Initializes select2 fields
function initSelect2() {
    $('.select2').select2({
        placeholder: ""
    });

    $('.select2.add').select2({
        tags: true,
        placeholder: ""
    });
}

// Initializes summernote wysiwyg editor
function initWysiwyg() {
    var lang = '';

    if ($('html').attr('lang') == 'de') {
        lang = 'de-DE';
    }

    $('#description').summernote({
        height: 100,
        lang: lang,
        toolbar: [
            // [groupName, [list of button]]
            ['misc', ['undo', 'redo']],
            ['style', ['bold', 'italic', 'underline', 'clear']],
            ['insert', ['link']],
            ['fontsize', ['fontsize']],
            ['para', ['ul', 'ol', 'paragraph']],
            ['misc', ['codeview', 'help']]
        ]
    });
}
