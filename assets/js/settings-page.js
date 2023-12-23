(function ($) {
    const plugin = ehami;
    const $container = $('#ehami-settings-page');
    const $form = $container.find('form');

    $form.on('submit', function (e) {
        e.preventDefault();

        let data = {};

        $form.find('input').each(function () {
            data[this.name] = $(this).prop('checked') * 1;
        });

        save_options('save_settings', data);
    });

    function save_options(action, data) {
        $container.addClass('--saving');

        console.log(data);

        const xhr = $.post(
            ajaxurl,
            {
                'action': 'ehami_' + action,
                'options': data,
                '_ajax_nonce': plugin.nonce
            }
        );

        xhr.done(function () {
            $container.removeClass('--saving');
        });
    }

})(jQuery);