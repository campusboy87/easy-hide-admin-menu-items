(function ($) {
    var plugin = ehami;
    var $container = $('#ehami-settings-page');
    var $form = $container.find('form');

    $form.on('submit', function (e) {
        e.preventDefault();

        var data = {};

        $form.find('.ehami-settings__main input[type="checkbox"]').each(function () {
            data[this.name] = $(this).prop('checked') ? 1 : 0;
        });

        // Include select values (separators_mode, future experimental selects).
        $form.find('.ehami-settings__main select').each(function () {
            data[this.name] = $(this).val();
        });

        save_options('save_settings', data);
    });

    function save_options(action, data) {
        $container.addClass('--saving');

        var xhr = $.post(
            ajaxurl,
            {
                'action': 'ehami_' + action,
                'options': data,
                '_ajax_nonce': plugin.nonce
            }
        );

        xhr.done(function () {
            $container.removeClass('--saving');

            // Apply hide_submenu change immediately in DOM.
            applyHideSubmenu(!!data.hide_submenu);

            showNotice('success', plugin.saved || 'Settings saved.');
        });

        xhr.fail(function () {
            $container.removeClass('--saving');
            showNotice('error', plugin.save_error || 'Error saving settings.');
        });
    }

    /**
     * Toggle submenu hide icons in sidebar without page reload.
     *
     * @param {boolean} enabled
     */
    function applyHideSubmenu(enabled) {
        var $sidebar = $('#adminmenu');
        var $subItems = $('.wp-submenu > li', $sidebar);

        if (enabled && !plugin.hide_submenu) {
            // Add icons if they don't exist yet.
            $subItems.each(function () {
                if (!$(this).find('.ehami-remove-sub-li').length) {
                    $(this).prepend(
                        '<span title="' + plugin.hid + '" class="dashicons dashicons-hidden ehami-remove-sub-li"></span>'
                    );
                }
            });
            plugin.hide_submenu = true;
        } else if (!enabled && plugin.hide_submenu) {
            // Remove icons.
            $subItems.find('.ehami-remove-sub-li').remove();
            plugin.hide_submenu = false;
        }
    }

    /**
     * Show a temporary WP-style admin notice.
     *
     * @param {string} type    "success" or "error"
     * @param {string} message
     */
    /**
     * Reset menu order — removes saved order, restores WP defaults.
     */
    $('#ehami-reset-menu-order').on('click', function (e) {
        e.preventDefault();

        var $link = $(this);
        $link.css('opacity', 0.5);

        $.post(ajaxurl, {
            action: 'ehami_reset_menu_order',
            _ajax_nonce: plugin.nonce
        }).done(function () {
            $link.css('opacity', 1);
            showNotice('success', plugin.saved || 'Settings saved.');
        }).fail(function () {
            $link.css('opacity', 1);
            showNotice('error', plugin.save_error || 'Error.');
        });
    });

    function showNotice(type, message) {
        var $existing = $container.find('.ehami-notice');
        $existing.remove();

        var $notice = $(
            '<div class="notice notice-' + type + ' is-dismissible ehami-notice">' +
                '<p>' + message + '</p>' +
                '<button type="button" class="notice-dismiss"></button>' +
            '</div>'
        );

        $container.find('.wp-header-end').after($notice);

        $notice.on('click', '.notice-dismiss', function () {
            $notice.fadeOut(200, function () { $notice.remove(); });
        });

        setTimeout(function () {
            $notice.fadeOut(400, function () { $notice.remove(); });
        }, 3000);
    }

})(jQuery);
