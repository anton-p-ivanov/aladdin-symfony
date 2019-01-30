$(function () {

    $('#ticket_os input:radio').on('change', function () {
        $('#os_instructions > div')
            .toggleClass('d-none', true)
            .toggleClass('d-block', false)
            .find('input')
            .attr('disabled', true);

        $('#os_instructions > div[data-os="' + $(this).val() + '"]')
            .toggleClass('d-none', false)
            .toggleClass('d-block', true)
            .find('input')
            .attr('disabled', false);
    });

    let checked = $('#ticket_os input:checked').val();
    $('#os_instructions > div')
        .toggleClass('d-none', true)
        .toggleClass('d-block', false)
        .find('input')
        .attr('disabled', true);

    $('#os_instructions > div[data-os="' + checked + '"]')
        .toggleClass('d-none', false)
        .toggleClass('d-block', true)
        .find('input')
        .attr('disabled', false);

});
