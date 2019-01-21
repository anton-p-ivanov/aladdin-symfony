(function($){

    let widgets = [];
    let request = function (widget) {
        $.ajax({
            url: widget.data('url'),
            headers: {
                'Widget-Request-Url': widget.data('endpoint'),
                'Widget-Search-Conditions': JSON.stringify(widget.data('conditions')),
                'Widget-Template': widget.data('template')
            },
            success: function (response) {
                widget.replaceWith(response);
            },
            complete: function () {
                let widget = widgets.shift();
                if (widget) {
                    request(widget);
                }
            },
        });
    };

    $.fn.widget = function() {

        this.each(function () {
            widgets.push($(this));
        });

        let widget = widgets.shift();
        if (widget) {
            request(widget);
        }

        return this;
    };

})(jQuery);