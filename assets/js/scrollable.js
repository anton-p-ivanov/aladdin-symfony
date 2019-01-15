(function($){

    $.fn.scrollable = function() {

        this.on("click", function (e) {
            let $$ = $(e.target),
                href = $$.attr("href");

            if (href && href.substring(0, 1) === "#") {
                e.preventDefault();
                let $id = $(href);
                if ($id.length) {
                    let top = $id.offset().top;
                    $("html, body").animate({scrollTop: top}, {'duration': 500, 'easing': "swing"});
                }
            }
        });

        return this;
    };

    $('.scrollable').scrollable();

})(jQuery);