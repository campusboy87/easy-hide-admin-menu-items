(function ($) {
    const plugin = ehami;

    var $sidebar = $('#adminmenu');
    var $sidebarItems = $('li.menu-top', $sidebar);
    var $sidebarItemsParents = $('.wp-submenu > li', $sidebar);

    var $barHami = $('#wp-admin-bar-ehami-switch');
    var $btnSwitch = $('#switch-flat', $barHami);

    var noItems = '<p class="no-items">' + plugin.no_items + '</p>';

    // Add a button to the sidebar menu items.
    $sidebarItems.prepend('<span title="' + plugin.hid + '" class="dashicons dashicons-hidden ehami-remove-li"></span>');

    // Hide a menu item in the sidebar on click and add information to the admin bar.
    $sidebarItems.on('click', '.ehami-remove-li', function () {
        var $li = $(this).closest('li');
        addItemAdminBar($li);
    });

    // Check if the hide_submenu option is set
    if (plugin.hide_submenu) {
        // Add a button to the sidebar submenu menu items.
        $sidebarItemsParents.prepend('<span title="' + plugin.hid + '" class="dashicons dashicons-hidden ehami-remove-sub-li"></span>');
    }

    // Hide a menu item in the sidebar on click and add information to the admin bar.
    $sidebarItemsParents.on('click', '.ehami-remove-sub-li', function () {
        var $li = $(this).closest('li');
        addItemAdminBar($li);
    });

    // Hide or show selected menu items in the sidebar.
    $btnSwitch.click(function () {
        var status = $(this).prop('checked');
        showRemoveIcon(status, true);
    });

    // Restore the visibility of a hidden menu item.
    $barHami.on('click', '.ehami-restore-li', function () {
        console.log('$barHami');
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

        save_options('remove_item', {'id': id});
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

            if (!id) {
                return;
            }

            var selector;
            if (id.includes('.php')) {
                selector = 'a[href="' + id + '"]';
            } else {
                selector = id;
            }

            var $el = $(selector, $sidebar);

            if (status) {
                if (id.includes('.php')) {
                    $el.each(function () {
                        if (!$(this).hasClass('wp-has-submenu')) {
                            $(this).fadeOut(300, function () {
                                $(this).css('display', 'none');
                                $('body').addClass('ehami-enable');
                            });
                        }
                    });
                } else {
                    $el.fadeOut(300, function () {
                        $(this).css('display', 'none');
                        $('body').addClass('ehami-enable');
                        $(this).closest('li').fadeOut(300);
                    });
                }
            } else {
                $el.fadeIn(300, function () {
                    $(this).css('display', 'block');
                    $('body').removeClass('ehami-enable');
                    $(this).closest('li').fadeIn(300);
                });
            }

        });

        save && save_options('save_status', {'status': status * 1});
    }

    /**
     * Save data.
     *
     * @param {string} action   Action (status, remove_item, add_item).
     * @param {object} data     Data
     */
    function save_options(action, data) {
        $.post(
            ajaxurl,
            {
                'action': 'ehami_' + action,
                'options': data,
                '_ajax_nonce': plugin.nonce
            }
        );
    }

    /**
     * Add an element to the admin bar.
     *
     * @param {jQuery} $li
     */
    function addItemAdminBar($li) {
        let $e, id, text, $template, item;

        $e = $($li).clone();
        $e.find('span').remove();

        id = '#' + $e.attr('id');
        text = $('.wp-menu-name', $e).text();

        const hasParentSubmenu = $li.closest('.wp-submenu').length > 0;
        if (hasParentSubmenu) {
            id = $e.find('a').attr('href');
            const $menuName = $li.closest('.wp-has-submenu').find('.wp-menu-name');
            const $menuNameClone = $menuName.clone().find('span').remove().end();
            text = $menuNameClone.text() + ' > ' + $($e).text();
        }

        item = '<span class="text">' + text + '</span>';
        item += ' <span class="dashicons dashicons-no ehami-restore-li"></span>';
        item = '<p data-id="' + id + '">' + item + '</p>';

        $template = $(item).css('display', 'none').show(300);

        $('.switch__content', $barHami).prepend($template);

        setTimeout(function () {
            $li.effect("transfer", {to: $('[data-id="' + id + '"]', $barHami)}, 500).hide(200);
        }, 300);

        recountItemsBar('plus');

        save_options('add_item', {
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
        var count = parseInt(plugin.count_items);
        plugin.count_items = action === 'plus' ? ++count : --count;

        if (plugin.count_items === 0) {
            $btnSwitch.prop('checked', false);
            $('.switch__content', $barHami).prepend(noItems);
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

    /**
     * Handle click events for dynamic elements inside .switch__content
     */
    $(document).on("click", ".switch__content p[data-id]", function(event) {

        event.preventDefault();

        // Check if the click was on the .ehami-restore-li element
        if ($(event.target).hasClass('ehami-restore-li')) {
            return;
        }

        var dataId = $(this).data("id");

        // Check if data-id contains '#'
        if (dataId.includes("#")) {
            var targetId = dataId;
            var $targetElement = $(targetId);
            console.log($targetElement);
            if ($targetElement.length) {
                // Find the first anchor tag inside the target element
                var $link = $targetElement.find("a");
                if ($link.length) {
                    $link.get(0).click();
                } else {
                    console.log("No link found inside the target element.");
                }
            } else {
                console.log("Target element with id " + targetId + " not found.");
            }
        } else {
            // No '#' in data-id, assuming it's a URL
            window.location.href = dataId;
        }
    });

    /**
     * Toggle the visibility of elements based on the checkbox state and save the option via AJAX.
     */
    function toggleEhamiHideIcons() {
        var isChecked = $('#hide-icons-checkbox').is(':checked');

        if (isChecked) {
            $('.ehami-remove-li, .ehami-remove-sub-li').hide();
        } else {
            $('.ehami-remove-li, .ehami-remove-sub-li').show();
        }

        save_options('hide_icons_disable', {'hide_icons_disable': isChecked});
    }

    toggleEhamiHideIcons()

    $('#hide-icons-checkbox').change(function() {
        toggleEhamiHideIcons();
    });


})(jQuery);