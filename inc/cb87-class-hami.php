<?php
defined('ABSPATH') || exit;

/**
 * Class CB87_Hide_Admin_Menu_Items
 *
 * Main plugin class responsible for hiding admin menu items and providing settings for super administrators.
 *
 * This class encapsulates the functionality related to hiding specific admin menu items based on user preferences.
 * It includes features such as adding a settings page for super administrators to customize menu settings individually.
 *
 * @since 1.0.0
 */
class CB87_Hide_Admin_Menu_Items
{

    /**
     * URL to the plugin folder with a trailing slash.
     *
     * @var string
     */
    public $url;

    /**
     * Application status.
     *
     * @var boolean
     */
    public $status;

    /**
     * Module settings.
     *
     * @var array
     */
    public $options;

    /**
     * Menu items.
     *
     * @var array
     */
    public $items;

    /**
     * Adds settings page to the "Settings" submenu for super administrators.
     *
     * @return void
     * @since 1.0.0
     *
     */
    public function cb87_add_settings_page()
    {
        if (is_super_admin()) {
            add_submenu_page(
                'options-general.php',
                __('Menu Settings', 'cb87-hami'),
                __('Menu Settings', 'cb87-hami'),
                'manage_options',
                'cb87-hami-settings',
                [$this, 'cb87_render_settings_page']
            );
        }
    }

    /**
     * Renders the settings page.
     *
     * @return void
     * @since 1.0.0
     *
     */
    public function cb87_render_settings_page()
    {
        ?>

        <div class="wrap cb87-settings">
            <h1 class="wp-heading-inline cb87-settings__heading"><?php esc_html_e('CB87 Hide Admin Menu Items Settings', 'cb87-hami'); ?></h1>
            <hr class="wp-header-end">

            <form method="post" action="<?php echo esc_url( admin_url('options.php') ); ?>">
                <?php
                settings_fields('cb87_settings_group');
                do_settings_sections('cb87_settings_section');
                ?>

                <div id="cb87-settings-container" class="metabox-holder cb87-settings__container">
                    <div id="cb87-main-settings" class="stuffbox cb87-settings__main">
                        <h2 class="cb87-settings__main-title"><?php esc_html_e('Main Settings', 'cb87-hami'); ?></h2>
                        <div class="inside cb87-settings__main-content">
                            <table class="form-table">
                                <tbody>
                                <?php
                                $fields = array(
                                    array(
                                        'label' => esc_html__('User-specific settings', 'cb87-hami'),
                                        'id' => 'cb87_save_individually',
                                        'name' => 'save_individually',
                                        'value' => $this->options['save_individually'] ?? false,
                                        'description' => esc_html__('Allow other users to set their own preferences.', 'cb87-hami'),
                                        'help' => esc_html__('If enabled, each user with access to the admin panel can save hidden menu settings individually for their own preferences.', 'cb87-hami')
                                    ),
                                    array(
                                        'label' => esc_html__('Child Menu', 'cb87-hami'),
                                        'id' => 'cb87_hide_submenu',
                                        'name' => 'hide_submenu',
                                        'value' => $this->options['hide_submenu'] ?? false,
                                        'description' => esc_html__('Allow hiding child admin menus', 'cb87-hami'),
                                        'help' => esc_html__('If checked, you can hide the child menu. This may be useful when there are too many child menus causing distraction.', 'cb87-hami')
                                    ),
                                );

                                foreach ($fields as $field) {
                                    ?>
                                    <tr>
                                        <th scope="row">
                                            <label for="<?php echo esc_attr($field['id']); ?>">
                                                <?php echo esc_html($field['label']); ?>
                                            </label>
                                        </th>
                                        <td>
                                            <fieldset class="cb87-settings__main-fieldset">
                                                <legend class="screen-reader-text">
                                                    <span><?php echo esc_html($field['label']); ?></span>
                                                </legend>

                                                <div class="cb87-settings__main-field">
                                                    <label for="<?php echo esc_attr($field['id']); ?>">
                                                        <input type="checkbox" id="<?php echo esc_attr($field['id']); ?>" name="cb87_hide_admin_menu_items[<?php echo esc_attr($field['name']); ?>]" value="1" <?php checked($field['value'], '1'); ?>>                                                        <?php echo esc_html($field['description']); ?>
                                                    </label>
                                                </div>

                                                <div class="cb87-settings__main-field-description">
                                                    <p class="description"><?php echo esc_html($field['help']); ?></p>
                                                </div>
                                            </fieldset>
                                        </td>
                                    </tr>
                                    <?php
                                }
                                ?>
                                </tbody>
                            </table>
                            <?php submit_button(esc_html__('Save Settings', 'cb87-hami'), 'primary', 'submit_cb87_settings'); ?>
                        </div>
                    </div>

                    <!-- Added a block for the author's message -->
                    <div id="cb87-author-info" class="stuffbox cb87-settings__author">
                        <h2 class="cb87-settings__author-title"><?php esc_html_e('Message From the Author', 'cb87-hami'); ?></h2>
                        <div class="inside cb87-settings__author-content">
                            <p><?php esc_html_e('Thank you for using CB87 Hide Admin Menu Items! Your feedback and comments are highly valuable to us and will assist us in improving the plugin further.', 'cb87-hami'); ?></p>
                            <p><?php esc_html_e('For support, please contact our helpdesk.', 'cb87-hami'); ?></p>
                            <p>
                                <span class="dashicons dashicons-star-filled"></span>
                                <span class="dashicons dashicons-star-filled"></span>
                                <span class="dashicons dashicons-star-filled"></span>
                                <span class="dashicons dashicons-star-filled"></span>
                                <span class="dashicons dashicons-star-filled"></span>
                            </p>
                        </div>
                    </div>
                </div>
            </form>
        </div>
        <?php
    }

    /**
     * Initializes the plugin functionality.
     *
     * @since 1.0.0
     *
     */
    public function init()
    {
        $this->url = plugin_dir_url(dirname(__FILE__));
        $this->set_properties();
        $this->register_ajax_hooks();

        add_action('admin_bar_menu', [$this, 'add_switch_to_menu']);
        add_action('in_admin_footer', [$this, 'enqueue_assets']);
        add_action('admin_body_class', [$this, 'add_body_class']);

        // Adds settings page to the "Settings" submenu for super administrators.
        if (is_super_admin()) {
            add_filter('plugin_action_links', [$this, 'cb87_add_settings_link'], 10, 2);
            add_action('admin_notices', [$this, 'cb87_admin_notices']);
            add_action('admin_menu', [$this, 'cb87_add_settings_page']);
            add_action('admin_init', [$this, 'cb87_init_settings']);
        }
    }

    /**
     * Adds a link to the plugin settings.
     *
     * @param array $links Existing array of links.
     * @param string $file Plugin file name.
     *
     * @return array Array with the added link.
     */
    public function cb87_add_settings_link($links, $file)
    {
        $site_url = home_url();

        $settings_link = '<a href="' . $site_url . '/wp-admin/admin.php?options-general.php?page=cb87-hami-settings">' . __('Settings', 'cb87-hami') . '</a>';
        array_unshift($links, $settings_link);

        return $links;
    }

    /**
     * Displays admin notices.
     *
     * @return void
     * @since 1.0.0
     *
     */
    public function cb87_admin_notices()
    {
        settings_errors('cb87_settings_group');
    }

    /**
     * Initializes plugin settings.
     *
     * @return void
     * @since 1.0.0
     *
     */
    public function cb87_init_settings()
    {
        // Function code here
        register_setting('cb87_settings_group', 'cb87_hide_admin_menu_items', [$this, 'cb87_sanitize_options']);
        add_settings_section('cb87_settings_section', '', '', 'cb87-hami-settings');

        // Adding a hook for handling form submission
        add_action('admin_post_cb87_handle_form', [$this, 'cb87_handle_form_submission']);
    }

    /**
     * Handle form submission for CB87 settings.
     *
     * This function is hooked to the 'admin_post_cb87_handle_form' action,
     * which is triggered when the settings form is submitted.
     * It checks for the presence of the 'submit_cb87_settings' field in the POST data,
     * and if found, it invokes the 'save_options' method to process and save the submitted options.
     */
    public function cb87_handle_form_submission() {
        if (isset($_POST['submit_cb87_settings'])) {
            $this->save_options();
        }
    }

    /**
     * Sanitizes plugin options.
     *
     * @since 1.0.0
     *
     */
    public function cb87_sanitize_options($input)
    {
        // Function code here
        return $input;
    }

    /**
     * Fills the "options" property with data (module settings).
     *
     * @return void
     * @since 1.0.0
     *
     */
    public function set_properties()
    {

        $options = get_option('cb87_hide_admin_menu_items', array());

        if ($options && is_user_logged_in()) {
            $user_id = get_current_user_id();

            $user_options = get_user_meta($user_id, 'cb87_hide_admin_menu_users', true);

            if ($user_options && is_array($user_options)) {
                $this->options = $user_options;
                $this->items = !empty($this->options['items']) ? $this->options['items'] : [];
                $this->status = !empty($this->options['status']);
                return;
            }
        }

        $this->options = $options && is_array($options) ? $options : [];
        $this->items = !empty($this->options['items']) ? $this->options['items'] : [];
        $this->status = !empty($this->options['status']);
    }

    /**
     * Saves plugin options.
     *
     * @return void
     * @since 1.0.0
     *
     */
    public function save_options()
    {
        error_log('Form submitted.');
        $user_id = get_current_user_id();
        $prev_options = get_user_meta($user_id, 'cb87_hide_admin_menu_users', true);

        $save_individually = isset($_POST['cb87_hide_admin_menu_items']['save_individually']) && $_POST['cb87_hide_admin_menu_items']['save_individually'] === '1';
        $hide_submenu = isset($_POST['cb87_hide_admin_menu_items']['hide_submenu']) && $_POST['cb87_hide_admin_menu_items']['hide_submenu'] === '1';

        error_log('save_individually: ' . var_export($save_individually, true));
        error_log('hide_submenu: ' . var_export($hide_submenu, true));

        // Проверяем, отмечена ли галочка
        if (isset($this->options['save_individually']) && $this->options['save_individually'] && $user_id) {
            update_user_meta($user_id, 'cb87_hide_admin_menu_users', $this->options);
        } else {
            // Очищаем метаданные пользователя, если галочка не отмечена
            delete_user_meta($user_id, 'cb87_hide_admin_menu_users');

            // Очищаем общие опции, если галочка была изменена
            if ($prev_options && !empty($prev_options)) {
                delete_option('cb87_hide_admin_menu_items');
            }
        }

        // Обновляем общие опции, если галочка не отмечена
        if (!$this->options['save_individually']) {
            update_option('cb87_hide_admin_menu_items', $this->options, false);
        }

    }

    /**
     * Adds a status class to the body tag based on the plugin status.
     *
     * @param string $classes
     *
     * @return  string $classes
     * @since 1.0.0
     *
     */
    public function add_body_class($classes)
    {
        return $this->status || empty($this->items) ? $classes . ' hami-enable' : $classes;
    }

    /**
     * Adds a switch to the admin bar.
     *
     * @param WP_Admin_Bar $wp_admin_bar
     * @since 1.0.0
     *
     */
    public function add_switch_to_menu($wp_admin_bar)
    {
        $wp_admin_bar->add_menu([
            'id' => 'hami-switch',
            'parent' => 'top-secondary',
            'title' => $this->get_switch_html(),
        ]);
    }

    /**
     * Returns HTML block with the switch.
     *
     * @return string
     * @since 1.0.0
     *
     */
    public function get_switch_html()
    {
        ob_start();
        ?>

        <form class="switch__container">
            <input id="switch-flat" class="switch" type="checkbox"<?php checked($this->status); ?>>
            <label for="switch-flat"
                   title="<?php esc_attr_e('Hide or show selected menu items', 'cb87-hami'); ?>"></label>
            <div class="switch__content">
                <?php
                if (!empty($this->items)) {
                    foreach ($this->items as $id => $text) {
                        $id = str_replace(['\"', ' '], ['\'', ''], $id);
                        printf('<p data-id="%s">
                                <span class="text">%s</span>
                                <span class="dashicons dashicons-no hami-restore-li" title="%s"></span>
                            </p>', $id, esc_html($text), esc_attr__('Remove from the list', 'cb87-hami'));
                    }
                } else {
                    echo '<p class="no-items">' . esc_html__('No hidden menu items', 'cb87-hami') . '</p>';
                }
                ?>
            </div>
        </form>

        <?php
        return ob_get_clean();
    }

    /**
     * Outputs CSS and JavaScript to the screen.
     *
     * @return void
     * @since 1.0.0
     *
     */
    public function enqueue_assets()
    {
        wp_enqueue_style('hami-style', $this->url . 'assets/css/hami-style.css');
        $this->inline_style('hami-style');

        wp_enqueue_script('hami-script', $this->url . 'assets/js/hami-script.js', [
            'jquery',
            'jquery-effects-transfer'
        ], null, true);

        wp_localize_script('hami-script', 'hami', [
            'nonce' => wp_create_nonce('hami-nonce'),
            'no_items' => __('No hidden menu items', 'cb87-hami'),
            'hid' => __('Hide menu', 'cb87-hami'),
            'count_items' => count($this->items),
            'hide_submenu' => isset($this->options['hide_submenu']) ? $this->options['hide_submenu'] : false,
        ]);
    }

    /**
     * Checks if the given string contains '.php'.
     *
     * @param string $str The string to check.
     *
     * @return bool True if the string contains '.php', false otherwise.
     * @since 1.0.0
     *
     */
    private function containsPHP($str)
    {
        // Determine if the PHP version supports str_contains
        return version_compare(PHP_VERSION, '8.0.0', '>=') ? str_contains($str, '.php') : strpos($str, '.php') !== false;
    }

    /**
     * Adds inline styles to hide menu items.
     *
     * @param string $handle
     *
     * @return void
     * @since 1.0.1
     *
     */
    public function inline_style($handle)
    {
        if ($this->items) {
            $css = '';
            foreach ($this->items as $id => $text) {
                if ($this->containsPHP($id)) {
                    $css .= sprintf('.hami-enable #adminmenu .wp-submenu a[href^="%s"] { display: none; }', esc_attr($id));
                } elseif (str_contains($id, '#')) {
                    $css .= sprintf('.hami-enable #adminmenu %s { display: none; }', esc_attr($id));
                }
            }
            wp_add_inline_style($handle, $css);
        }
    }

    /**
     * Retrieves options from the global POST array.
     *
     * @param string $key Data key.
     *
     * @return array
     * @since 1.0.0
     *
     */
    public function get_ajax_options($key)
    {
        $data = map_deep($_POST, function ($val) {
            return trim(sanitize_text_field($val));
        });

        return isset($data['options'][$key]) ? $data['options'][$key] : [];
    }

    /**
     * Registers AJAX handlers.
     *
     * @return void
     * @since 1.0.0
     *
     */
    public function register_ajax_hooks()
    {
        if (defined('DOING_AJAX') && DOING_AJAX) {
            add_action('wp_ajax_hami_save_status', [$this, 'ajax_save_status']);
            add_action('wp_ajax_hami_add_item', [$this, 'ajax_add_item']);
            add_action('wp_ajax_hami_remove_item', [$this, 'ajax_remove_item']);
        }
    }

    /**
     * Saves the application status.
     *
     * @return void
     * @since 1.0.0
     *
     */
    public function ajax_save_status()
    {
        $this->check_ajax_referer();

        $status = $this->get_ajax_options('status');

        if ($status === '1' || $status === '0') {
            $this->options['status'] = $status;

            $current_options = get_option('cb87_hide_admin_menu_items', false);

            if (!empty($this->options['save_individually'])) {
                $users = get_users();

                foreach ($users as $user) {
                    update_user_meta($user->ID, 'cb87_hide_admin_menu_users', $this->options);
                }
            } else {
                update_option('cb87_hide_admin_menu_items', array_merge($current_options, $this->options), false);
            }
        }

        wp_die();
    }

    /**
     * Saves data about the menu item to be hidden.
     *
     * @return void
     * @since 1.0.1
     */
    public function ajax_add_item()
    {
        $this->check_ajax_referer();

        $data = $this->get_ajax_options('item');

        if (!empty($data['id']) && !empty($data['text'])) {
            $this->options['items'][$data['id']] = $data['text'];
            $this->options['status'] = true;
            $this->save_options();
        }

        wp_die();
    }

    /**
     * Removes data about the menu item to hide via AJAX.
     *
     * @return void
     * @since 1.0.0
     *
     */
    public function ajax_remove_item()
    {
        $this->check_ajax_referer();

        $id = $this->get_ajax_options('id');

        // Ensure $id is a string before using it as an array key
        if (is_string($id)) {
            unset($this->options['items'][$id]);

            if ($this->status) {
                $this->options['status'] = $this->options['items'] && is_array($this->options['items']);
            }

            $this->save_options();
        }

        wp_die();
    }

    /**
     * Checks the Ajax request for nonce code compliance.
     *
     * @return void
     * @since 1.0.0
     *
     */
    public function check_ajax_referer()
    {
        check_ajax_referer('hami-nonce');
    }
}
