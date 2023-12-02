<?php
defined( 'ABSPATH' ) || exit;

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
class CB87_Hide_Admin_Menu_Items {

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
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function cb87_add_settings_page() {
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
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function cb87_render_settings_page() {
		?>
		<div class="wrap cb87-settings">
			<h1 class="wp-heading-inline cb87-settings__heading"><?php esc_html_e('CB87 Hide Admin Menu Items Settings', 'cb87-hami'); ?></h1>
			<hr class="wp-header-end">

			<form method="post" action="options.php">
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
								<tr>
									<th scope="row">
										<label for="cb87_save_individually">
											<?php esc_html_e('User-specific settings', 'cb87-hami'); ?>
										</label>
									</th>
									<td>
										<fieldset class="cb87-settings__main-fieldset">
											<legend class="screen-reader-text">
												<span><?php esc_html_e('Main Settings', 'cb87-hami'); ?></span>
											</legend>

											<div class="cb87-settings__main-field">
												<label for="cb87_save_individually">
													<input type="checkbox" id="cb87_save_individually" name="cb87_hide_admin_menu_items[save_individually]" value="1" <?php checked(isset($this->options['save_individually']) && $this->options['save_individually']); ?>>
													<?php esc_html_e('Save settings individually for each user', 'cb87-hami'); ?>
												</label>
											</div>

											<div class="cb87-settings__main-field-description">
												<p class="description"><?php esc_html_e('If checked, each user can save settings individually.', 'cb87-hami'); ?></p>
											</div>
										</fieldset>
									</td>

								</tr>
								</tbody>
							</table>

							<?php submit_button(esc_html__('Save Settings', 'cb87-hami')); ?>
						</div>
					</div>

					<!-- Added a block for the author's message -->
					<div id="cb87-author-info" class="stuffbox cb87-settings__author">
						<h2 class="cb87-settings__author-title"><?php esc_html_e('Message From the Author', 'cb87-hami'); ?></h2>
						<div class="inside cb87-settings__author-content">
							<p><?php esc_html_e('Thank you for using CB87 Hide Admin Menu Items! If you find the plugin helpful, please consider leaving a 5-star review on the repository.', 'cb87-hami'); ?></p>
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
	 * @return void
	 */
	public function init() {
		$this->url = plugin_dir_url( dirname( __FILE__ ) );
		$this->set_properties();
		$this->register_ajax_hooks();

		add_action( 'admin_bar_menu', [ $this, 'add_switch_to_menu' ] );
		add_action( 'in_admin_footer', [ $this, 'enqueue_assets' ] );
		add_action( 'admin_body_class', [ $this, 'add_body_class' ] );

		// Adds settings page to the "Settings" submenu for super administrators.
		if ( is_super_admin() ) {
			add_filter( 'plugin_action_links', [ $this, 'cb87_add_settings_link' ], 10, 2 );
			add_action( 'admin_notices', [ $this, 'cb87_admin_notices' ] );
			add_action( 'admin_menu', [ $this, 'cb87_add_settings_page' ] );
			add_action( 'admin_init', [ $this, 'cb87_init_settings' ] );
		}
	}

	/**
	 * Adds a link to the plugin settings.
	 *
	 * @param array  $links Existing array of links.
	 * @param string $file  Plugin file name.
	 *
	 * @return array Array with the added link.
	 */
	public function cb87_add_settings_link( $links, $file ) {
		$site_url = home_url();

		$settings_link = '<a href="' . $site_url . '/wp-admin/admin.php?options-general.php?page=cb87-hami-settings">' . __( 'Settings', 'cb87-hami' ) . '</a>';
		array_unshift( $links, $settings_link );

		return $links;
	}

	/**
	 * Displays admin notices.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function cb87_admin_notices() {
		settings_errors('cb87_settings_group');
	}

	/**
	 * Initializes plugin settings.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function cb87_init_settings() {
		// Function code here
		register_setting('cb87_settings_group', 'cb87_hide_admin_menu_items', [$this, 'cb87_sanitize_options']);
		add_settings_section('cb87_settings_section', '', '', 'cb87-hami-settings');
	}

	/**
	 * Sanitizes plugin options.
	 *
	 * @since 1.0.0
	 *
	 * @param array $input Options to sanitize.
	 *
	 * @return array
	 */
	public function cb87_sanitize_options($input) {
		// Function code here
		return $input;
	}

	/**
	 * Fills the "options" property with data (module settings).
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function set_properties() {

		$user_specific_menu = get_option('cb87_hide_admin_menu_items', false);

		// Проверяем, отмечена ли галочка
		if ($user_specific_menu && is_user_logged_in()) {
			$user_id = get_current_user_id();
			$user_options = get_user_meta($user_id, 'cb87_hide_admin_menu_users', true);

			// Если есть опции для пользователя, используем их
			if ($user_options && is_array($user_options)) {
				$this->options = $user_options;
				$this->items   = !empty($this->options['items']) ? $this->options['items'] : [];
				$this->status  = !empty($this->options['status']);
				return;
			}
		}

		// В противном случае используем общие опции
		$options = get_option('cb87_hide_admin_menu_items', array());
		$this->options = $options && is_array($options) ? $options : [];
		$this->items   = !empty($this->options['items']) ? $this->options['items'] : [];
		$this->status  = !empty($this->options['status']);
	}

	/**
	 * Saves plugin options.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function save_options() {
		$user_id      = get_current_user_id();
		$prev_options = get_user_meta($user_id, 'cb87_hide_admin_menu_users', true);

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
	 * @since 1.0.0
	 *
	 * @param string $classes
	 *
	 * @return  string $classes
	 */
	public function add_body_class( $classes ) {
		return $this->status || empty( $this->items ) ? $classes . ' hami-enable' : $classes;
	}

	/**
	 * Adds a switch to the admin bar.
	 *
	 * @since 1.0.0
	 *
	 * @param WP_Admin_Bar $wp_admin_bar
	 */
	public function add_switch_to_menu( $wp_admin_bar ) {
		$wp_admin_bar->add_menu( [
			'id'     => 'hami-switch',
			'parent' => 'top-secondary',
			'title'  => $this->get_switch_html(),
		] );
	}

	/**
	 * Returns HTML block with the switch.
	 *
	 * @since 1.0.0
	 *
	 * @return string
	 */
	public function get_switch_html() {
		ob_start();
		?>

		<form class="switch__container">
			<input id="switch-flat" class="switch" type="checkbox"<?php checked( $this->status ); ?>>
			<label for="switch-flat" title="<?php esc_attr_e( 'Hide or show selected menu items', 'cb87-hami' ); ?>"></label>
			<div class="switch__content">
				<?php
				if ( ! empty( $this->items ) ) {
					foreach ( $this->items as $id => $text ) {
						printf( '<p data-id="%s">
                                <span class="text">%s</span>
                                <span class="dashicons dashicons-no hami-restore-li" title="%s"></span>
                            </p>', $id, esc_html( $text ), esc_attr__( 'Remove from the list', 'cb87-hami' ) );
					}
				} else {
					echo '<p class="no-items">' . esc_html__( 'No hidden menu items', 'cb87-hami' ) . '</p>';
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
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function enqueue_assets() {
		wp_enqueue_style( 'hami-style', $this->url . 'assets/hami-style.css' );
		$this->inline_style( 'hami-style' );

		wp_enqueue_script( 'hami-script', $this->url . 'assets/hami-script.js', [
			'jquery',
			'jquery-effects-transfer'
		], null, true );

		wp_localize_script( 'hami-script', 'hami', [
			'nonce'       => wp_create_nonce( 'hami-nonce' ),
			'no_items'    => __( 'No hidden menu items', 'cb87-hami' ),
			'count_items' => count( $this->items ),
		] );
	}

	/**
	 * Adds inline styles to hide menu items.
	 *
	 * @since 1.0.0
	 *
	 * @param string $handle
	 *
	 * @return void
	 */
	public function inline_style( $handle ) {
		if ( $this->items ) {
			$css = '';
			foreach ( $this->items as $id => $text ) {
				$css .= ".hami-enable #$id { display: none; }";
			}

			wp_add_inline_style( $handle, $css );
		}
	}

	/**
	 * Retrieves options from the global POST array.
	 *
	 * @since 1.0.0
	 *
	 * @param string $key Data key.
	 *
	 * @return array
	 */
	public function get_ajax_options( $key ) {
		$data = map_deep( $_POST, function ( $val ) {
			return trim( sanitize_text_field( $val ) );
		} );

		return isset( $data['options'][ $key ] ) ? $data['options'][ $key ] : [];
	}

	/**
	 * Registers AJAX handlers.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function register_ajax_hooks() {
		if ( defined( 'DOING_AJAX' ) && DOING_AJAX ) {
			add_action( 'wp_ajax_hami_save_status', [ $this, 'ajax_save_status' ] );
			add_action( 'wp_ajax_hami_add_item', [ $this, 'ajax_add_item' ] );
			add_action( 'wp_ajax_hami_remove_item', [ $this, 'ajax_remove_item' ] );
		}
	}

	/**
	 * Saves the application status.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function ajax_save_status() {
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
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function ajax_add_item() {

		$this->check_ajax_referer();

		$data = $this->get_ajax_options( 'item' );

		if ( ! empty( $data['id'] ) && ! empty( $data['text'] ) ) {

			$this->options['items'][ $data['id'] ] = $data['text'];
			$this->options['status']               = true;
			$this->save_options();
		}

		wp_die();
	}

	/**
	 * Removes data about the menu item to hide via AJAX.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function ajax_remove_item() {
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
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function check_ajax_referer() {
		check_ajax_referer('hami-nonce');
	}
}
