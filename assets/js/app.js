import "jquery";
import "bootstrap";
import "./scrollable";
import "./widget";

$(function () {
    $('[data-widget="api"]').widget();

    let methods = {
        toggleSpy: function () {
            let condition = $(document).scrollTop() >= $('#leadspace').height() + $('#header').height();
            $('#scroll-spy-nav').toggleClass('active', condition);
        }
    };

    $(document).on("scroll", function () {
        methods.toggleSpy();
    });

    methods.toggleSpy();

    $('a[data-toggle="tab"]').on('shown.bs.tab', function (e) {
        let trigger = $(e.target),
            target = $(trigger.data('target'));

        if (trigger.data('remote') && !target.data('loaded')) {
            target.load(trigger.attr('href'), function () {
                target.data('loaded', true);
                target.find('[data-widget="api"]').widget();
            });
        }

        if (target.length && !trigger.is('.nav-link')) {
            let tab = $('.nav a').filter('[data-target="' + trigger.data('target') + '"]'),
                navigation = tab.closest('.nav');

            navigation.find('.nav-item.active').toggleClass('active', false);
            if (tab.is('.nav-item')) {
                tab.toggleClass('active', true);
            }
            else {
                tab.closest('.nav-item').toggleClass('active', true);
            }

            $("html, body").animate({scrollTop: target.offset().top}, {'duration': 500, 'easing': "swing"});
        }
    });

    $('.modal').on('show.bs.modal', function (e) {
        let trigger = $(e.relatedTarget),
            hash = e.relatedTarget.hash,
            target = $(e.target);

        if (hash) {
            $('.nav a[href="' + hash + '"]').tab('show');
        }

        if (trigger.data('remote') && !target.data('loaded')) {
            target.load(trigger.attr('href'), function () {
                target.data('loaded', true);
                if (hash) {
                    $('.nav a[href="' + hash + '"]').tab('show');
                }
            });
        }
    });
});

