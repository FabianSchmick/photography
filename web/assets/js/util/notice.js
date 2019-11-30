import Cookies from "js-cookie";

/**
 * Notice for cookie usage
 */
export function notice() {
    let $body = $("body"),
        $notice = $("#notice");

    // check cookie
    if ($notice.length > 0) {
        if (Cookies.get("Notice")) {
            $notice.remove();
        } else {
            $notice.addClass("show");
            $body.addClass("notice");
        }
    }

    // set cookie
    $(".agree").on("click", function(e) {
        $notice.remove();
        $body.removeClass("notice");
        Cookies.set("Notice", true, {
            path: "/",
            expire: 365
        });
        e.preventDefault();
    });
}
