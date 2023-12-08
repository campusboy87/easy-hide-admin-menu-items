(function ($) {
    var $sidebar = jQuery('#adminmenu');
    var $sidebarItems = $('li.menu-top', $sidebar);

    var $barHami = $('#wp-admin-bar-hami-switch');
    var $btnSwitch = $('#switch-flat', $barHami);

    var noItems = '<p class="no-items">' + hami.no_items + '</p>';

    // Add a button to the sidebar menu items.
    $sidebarItems.prepend('<span title="' + hami.hid + '" class="dashicons dashicons-hidden hami-remove-li"></span>');

    // Hide a menu item in the sidebar on click and add information to the admin bar.
    $sidebarItems.on('click', '.hami-remove-li', function () {
        var $li = $(this).closest('li');
        addItemAdminBar($li);
    });

    // Hide or show selected menu items in the sidebar.
    $btnSwitch.click(function () {
        var status = $(this).prop('checked');
        showRemoveIcon(status, true);
    });

    // Restore the visibility of a hidden menu item.
    $barHami.on('click', '.hami-restore-li', function () {
        var $el = $(this).closest('p');
        var id = $el.data('id');

        $el.hide(150, function () {
            $el.remove();
        });

        $('#' + id, $sidebar).show(300);

        recountItemsBar('minus');

        save_option('remove_item', {'id': id});
    });

    /**
     * Show or hide remove icons for menu items in the sidebar.
     *
     * @param {boolean} status
     * @param {boolean} save
     */
    function showRemoveIcon(status, save) {
        $('.switch__content p', $barHami).each(function () {
            var id = $(this).data('id');
            var $el = $('[id=' + id + ']', $sidebar);

            if (status) {
                $el.hide(500, function () {
                    $('body').addClass('hami-enable');
                })
            } else {
                $el.show(500, function () {
                    $('body').removeClass('hami-enable');
                })
            }
        });

        save && save_option('save_status', {'status': status * 1});
    }

    /**
     * Save data.
     *
     * @param {string} action   Action (status, remove_item, add_item).
     * @param {object} data     Data
     */
    function save_option(action, data) {
        $.post(
            ajaxurl,
            {
                'action': 'hami_' + action,
                'options': data,
                '_ajax_nonce': hami.nonce
            }
        );
    }

    /**
     * Add an element to the admin bar.
     *
     * @param {jQuery} $li
     */
    function addItemAdminBar($li) {
        var $e, id, text, $template, item;

        $e = $($li).clone();
        $e.find('span').remove();

        id = $e.attr('id');
        text = $('.wp-menu-name', $e).text();

        item = '<span class="text">' + text + '</span>';
        item += ' <span class="dashicons dashicons-no hami-restore-li"></span>';
        item = '<p data-id="' + id + '">' + item + '</p>';

        $template = $(item).css('display', 'none').show(300);

        $('.switch__content', $barHami).append($template);

        setTimeout(function () {
            $li.effect("transfer", {to: $('[data-id=' + id + ']', $barHami)}, 500).hide(200);
        }, 300);

        recountItemsBar('plus');

        save_option('add_item', {
            'item': {
                'id': id,
                'text': text
            }
        });
    }

    /**
     * Recount the number of hidden menu items.
     *
     * @param {string} action
     */
    function recountItemsBar(action) {
        var count = parseInt(hami.count_items);
        hami.count_items = action === 'plus' ? ++count : --count;

        if (hami.count_items === 0) {
            $btnSwitch.prop('checked', false);
            $('.switch__content', $barHami).append(noItems);
        } else {
            $('.no-items', $barHami).remove();
        }

        if (action === 'plus') {
            $btnSwitch.prop('checked', true);
            animateSwitchContent();
        }
    }

    // SlideDown-> SlideUp animation of the block with hidden menu items.
    function animateSwitchContent() {
        $('.switch__content', $barHami).slideDown(300, function () {
            setTimeout(function () {
                $('.switch__content', $barHami).slideUp(150, function () {
                    $(this).removeAttr('style');
                });
            }, 500);
        });
    }

    // SlideDown-> SlideUp animation of the block with hidden menu items on hover.
    $barHami
        .mouseenter(function () {
            $('.switch__content', $barHami).stop(true).slideDown(250);
        })
        .mouseleave(function () {
            $('.switch__content', $barHami).stop(true).slideUp(200, function () {
                $(this).removeAttr('style');
            });
        });

})(jQuery);