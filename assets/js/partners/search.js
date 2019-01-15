$(function () {

    let
        // Form control
        $form = $('form[name="partners"]'),

        // Countries dropdown form control
        $country = $('#country'),

        // Cities dropdown form control
        $city = $('#city'),

        // Submit button
        $submit = $form.find('[type="submit"]'),

        // Search results list
        $results = $('#search-results'),

        // Cloak
        $cloak = $('.cloak'),

        // Support for History API
        isHistorySupported = !!(window.history && history.pushState),

        // History API state
        state = null,

        // Initial form state
        initialState = null,

        // Methods
        m = {

            // (Re)-builds cities list depends on selected country value
            buildCitiesList: function () {
                let value = $country.val();

                $city.attr('disabled', typeof cities[value] !== 'object');
                $city.find('option:not(:first)').remove();
                $.each(cities[value], function (code, name) {
                    $city.get(0).options[$city.get(0).options.length] = new Option(name, name);
                });
            },

            // Prepare URL params
            prepareUrlParams: function () {
                let params = [],
                    data = state ? state : m.getSubmittedData();

                $.each(data, function (index, value) {
                    params.push(value.name + "=" + encodeURIComponent(value.value));
                });

                return params.join('&');
            },

            // Get user selected data
            getSubmittedData: function () {
                return $form.serializeArray().filter(function(value) {
                    return value.value !== "" && value.name.charAt(0) !== '_';
                });
            },

            // Submit form via ajax
            submit: function () {
                let params = m.prepareUrlParams(),
                    url = $form.attr('action') + (params ? '?' + params : '');

                m.request(url, $form.serialize());
            },

            // Make Ajax request
            request: function (url, data) {
                $.ajax({
                    'url': url,
                    'type': 'POST',
                    'data': data || {},
                    'beforeSend': function () {
                        $submit.attr('disabled', true);
                        $cloak.toggleClass('active', true);
                    }
                }).always(function () {
                    $submit.attr('disabled', false);
                    $cloak.toggleClass('active', false);
                }).done(function (response) {
                    $results.html(response);
                });
            },

            update: function () {
                let data = initialState;

                if (state && typeof state === 'object') {
                    data = state;
                }

                $form.find('input:not(:checkbox),select').val(null);
                $.each(data, function (index, value) {
                    $('#' + value.name).val(value.value);
                });

                m.submit();
            }
        };

    initialState = m.getSubmittedData();

    // Attach country change handler
    $country.on("change", function () { m.buildCitiesList() });

    $city.on('change', function () {
        $('[data-update="city"]').text($(this).val());
    });

    // Attach form submit handler
    $form.on("submit", function (e) {
        e.preventDefault();

        // Clear History API state
        state = null;

        if (isHistorySupported) {
            let data = m.getSubmittedData(),
                params = m.prepareUrlParams(),
                url = $form.attr('action') + (params ? '?' + params : '');

            window.history.pushState(data, '', url);
        }

        // Call submit handler
        m.submit();
    });

    // Attach pager links handler
    $(document).on('click', '.page-link', function (e) {
        e.preventDefault();

        if (isHistorySupported) {
            let data = m.getSubmittedData();
                data.push({'name': 'page', 'value': $(e.target).data('page')});

            window.history.pushState(data, '', e.target.href);
        }

        m.request(e.target.href);
    });

    // Attach History API popstate handler
    $(window).on('popstate', function (e) {
        state = e.originalEvent.state;
        state = state === null ? false : state;

        m.update();
    });
});
