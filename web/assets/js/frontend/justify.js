import 'justifiedGallery';

/**
 * Justify entries
 */
export function justify() {
    $("[data-justified]").each((index, el) => {
        $(el).justifiedGallery({
            rowHeight: $(el).data('row-height') || 400,
            maxRowHeight: "175%",
            lastRow: "nojustify",
            margins: 5,
            imagesAnimationDuration: 1000,
            captions: false
        });
    });
}
