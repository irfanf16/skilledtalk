$(document).ready(function () {
    //jQuery time
    var current_fs, next_fs; //fieldsets
    var left, opacity, scale; //fieldset properties which we will animate
    var animating; //flag to prevent quick multi-click glitches

    $(".continue_button").click(function () {
        if (animating) return false;
        animating = true;

        current_fs = $(this).parent().parent();
        next_fs = $(this).parent().parent().next();

        //show the next fieldset
        next_fs.show();
        //hide the current fieldset with style
        current_fs.animate(
            { opacity: 0 },
            {
                step: function (now, mx) {
                    //as the opacity of current_fs reduces to 0 - stored in "now"
                    //1. scale current_fs down to 80%
                    scale = 1 - (1 - now) * 0.2;
                    //2. bring next_fs from the right(50%)
                    left = now * 50 + "%";
                    //3. increase opacity of next_fs to 1 as it moves in
                    opacity = 1 - now;
                    current_fs.css({
                        transform: "scale(" + scale + ")",
                        position: "absolute",
                        right: "0",
                        margin: "0 auto",
                        left: "0",
                    });
                    next_fs.css({ left: left, opacity: opacity });
                },
                duration: 800,
                complete: function () {
                    current_fs.hide();
                    animating = false;
                },
                //this comes from the custom easing plugin
                easing: "easeInOutBack",
            }
        );
    });
    $(".submit_button").click(function () {
        return false;
    });
});
$(window).on("load", function () {
    // makes sure the whole site is loaded
    $(".loading-section").fadeOut(); // will first fade out the loading animation
    $("#loading-page").delay(500).fadeOut("slow"); // will fade out the white DIV that covers the website.
    $("body").delay(500).css({ overflow: "hidden" });
});