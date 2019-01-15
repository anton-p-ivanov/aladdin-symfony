(function($){

    $.fn.loadContent = function() {

        this.on('click', function (e) {
            e.preventDefault();

            let trigger = $(this),
                target = $(trigger.data('target'));

            if (target.data('loaded') === true) {
                return;
            }

            trigger.toggleClass('disabled');
            trigger.children().toggleClass('d-flex').toggleClass('d-none');

            setTimeout(function () {
                $.ajax({
                    url: trigger.attr('href'),
                })

                .always (function () {
                    trigger.toggleClass('disabled');
                    trigger.children().toggleClass('d-flex').toggleClass('d-none')
                })

                .done(function (data) {
                    target.html(data);
                    target.data('loaded', true);

                    if (trigger.data('destroy') === true) {
                        trigger.remove();
                    }
                })

                .fail(function (xhr) {
                    target.html(xhr.responseText)
                });

            }, 750);

        });

        return this;
    };

})(jQuery);