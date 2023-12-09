(function ($) {
    var $sidebar = jQuery('#adminmenu');
    var $sidebarItems = $('li.menu-top', $sidebar);
    var $sidebarItemsParents = $('.wp-submenu > li', $sidebar);

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

    // Check if the hide_submenu option is set
    if (hami.hide_submenu) {
        // Add a button to the sidebar submenu menu items.
        $sidebarItemsParents.prepend('<span title="' + hami.hid + '" class="dashicons dashicons-hidden hami-remove-sub-li"></span>');
    }

    // Hide a menu item in the sidebar on click and add information to the admin bar.
    $sidebarItemsParents.on('click', '.hami-remove-sub-li', function () {
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

        var selector;
        if (id.includes('.php')) {
            selector = 'a[href^="' + id + '"]';
            $(selector).closest('li').fadeIn(300);
        } else {
            selector = id;
        }

        $(selector, $sidebar).fadeIn(300, function () {
            $(this).css('display', 'block');
        });

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

            var selector;
            if (id.includes('.php')) {
                selector = 'a[href^="' + id + '"]';
            } else {
                selector = id;
            }

            var $el = $(selector, $sidebar);

            /* TODO: Переделать и оптимизировать анимацию */

            if (status) {
                if (id.includes('.php')) {
                    $el.fadeOut(300, function () {
                        $(this).css('display', 'none');
                        $('body').addClass('hami-enable');
                    });
                } else {
                    $el.fadeOut(300, function () {
                        $(this).css('display', 'none');
                        $('body').addClass('hami-enable');
                        $(this).closest('li').fadeOut(300);
                    });
                }
            } else {
                $el.fadeIn(300, function () {
                    $(this).css('display', 'block');
                    $('body').removeClass('hami-enable');
                    $(this).closest('li').fadeIn(300);
                });
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

        id = '#' + $e.attr('id');
        text = $('.wp-menu-name', $e).text();

        var hasParentSubmenu = $li.closest('.wp-submenu').length > 0;
        if (hasParentSubmenu) {
            id = $e.find('a').attr('href');
            text = $li.closest('.wp-has-submenu').find('.wp-menu-name').text() + ' > ' + $($e).text();
        }

        item = '<span class="text">' + text + '</span>';
        item += ' <span class="dashicons dashicons-no hami-restore-li"></span>';
        item = '<p data-id="' + id + '">' + item + '</p>';

        $template = $(item).css('display', 'none').show(300);

        $('.switch__content', $barHami).append($template);

        setTimeout(function () {
            $li.effect("transfer", {to: $('[data-id="' + id + '"]', $barHami)}, 500).hide(200);
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