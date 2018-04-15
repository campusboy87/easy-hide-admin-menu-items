(function ($) {
    var $sidebar = jQuery('#adminmenu');
    var $sidebarItems = $('li.menu-top', $sidebar);

    var $barShm = $('#wp-admin-bar-shm-switch');
    var $btnSwitch = $('#switch-flat', $barShm);

    var noItems = '<p class="no-items">Нет скрытых пунктов меню</p>';

    // Добавляет кнопку в пункты меню сайдбара.
    $sidebarItems.prepend('<span title="Скрыть" class="dashicons dashicons-hidden shm-remove-li"></span>');

    // Скрывает пункт меню в сайдбаре по клику и добавляет об этом информацию в админ-бар.
    $sidebarItems.on('click', '.shm-remove-li', function () {
        var $li = $(this).closest('li');
        addItemAdminBar($li);
    });

    // Скрывает выбранные пункты меню или отображает их все в сайдбаре.
    $btnSwitch.click(function () {
        var status = $(this).prop('checked');
        showRemoveIcon(status, true);
    });

    // Восстанавливает видимость скрытого пункта меню.
    $barShm.on('click', '.shm-restore-li', function () {
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
     * Скрывает или отображает иконки удаления у пунков меню в сайдбаре.
     *
     * @param {boolean} status
     * @param {boolean} save
     */
    function showRemoveIcon(status, save) {
        if (status)
            $('body').addClass('shm-enable');
        else
            $('body').removeClass('shm-enable');

        save && save_option('save_status', {'status': status * 1});
    }

    /**
     * Сохраняет данные.
     *
     * @param {string} action   Действие (status, remove_item, add_item).
     * @param {object} data     Данные
     */
    function save_option(action, data) {
        $.post(
            ajaxurl,
            {
                'action': 'shm_' + action,
                'options': data,
                '_ajax_nonce': shm.nonce
            }
        );
    }

    /**
     *  Добавляет элемент в админ-бар.
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
        item += ' <span class="dashicons dashicons-no shm-restore-li"></span>';
        item = '<p data-id="' + id + '">' + item + '</p>';

        $template = $(item).css('display', 'none').show(300);

        $('.switch__content', $barShm).append($template);

        setTimeout(function () {
            $li.effect("transfer", {to: $('[data-id=' + id + ']', $barShm)}, 500).hide(200);
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
     * Пересчитывает количество скрытых пунктов меню.
     *
     * @param {string} action
     */
    function recountItemsBar(action) {
        var count = parseInt(shm.count_items);
        shm.count_items = action === 'plus' ? ++count : --count;

        if (shm.count_items === 0) {
            $btnSwitch.prop('checked', false);
            $('.switch__content', $barShm).append(noItems);
        } else {
            $btnSwitch.prop('checked', true);
            $('.no-items', $barShm).remove();
        }

        if (action === 'plus') {
            animateSwitchContent();
        }
    }

    // Анимация slideDown->slideUp блока со скрытыми пунктами меню.
    function animateSwitchContent() {
        $('.switch__content', $barShm).slideDown(300, function () {
            setTimeout(function () {
                $('.switch__content', $barShm).slideUp(150, function () {
                    $(this).removeAttr('style');
                });
            }, 500);
        });
    }

    // Анимация slideDown->slideUp у блока со списком скрытых пунктов меню при наведении мышкой.
    $barShm
        .mouseenter(function () {
            $('.switch__content', $barShm).stop(true).slideDown(250);
        })
        .mouseleave(function () {
            $('.switch__content', $barShm).stop(true).slideUp(200, function () {
                $(this).removeAttr('style');
            });
        });

})(jQuery);