(function($){

    $.fn.widget = function() {

        this.each(function () {
            let widget = $(this);

            $.ajax({
                url: widget.data('url'),
                headers: {
                    'Widget-Request-Url': widget.data('endpoint'),
                    'Widget-Search-Conditions': JSON.stringify(widget.data('conditions')),
                    'Widget-Template': widget.data('template')
                },
                success: function (response) {
                    widget.replaceWith(response);
                }
            });
        });

        return this;
    };

})(jQuery);