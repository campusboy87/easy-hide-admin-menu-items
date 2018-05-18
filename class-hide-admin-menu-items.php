<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

class Hide_Admin_Menu_Items {

	/**
	 * Ссылка на папку с плагином с закрывающим слешем на конце.
	 *
	 * @var string
	 */
	public $url;

	/**
	 * Статус приложения.
	 *
	 * @var boolean
	 */
	public $status;

	/**
	 * Настройки модуля.
	 *
	 * @var array
	 */
	public $options;

	/**
	 * Пункты меню.
	 *
	 * @var array
	 */
	public $items;

	/**
	 * Запускает функционал плагина.
	 *
	 * @return void
	 */
	public function init() {
		$this->url = plugin_dir_url( __FILE__ );
		$this->set_properties();
		$this->register_ajax_hooks();

		add_action( 'admin_bar_menu', [ $this, 'add_switch_to_menu' ] );
		add_action( 'in_admin_footer', [ $this, 'enqueue_assets' ] );
		add_action( 'admin_body_class', [ $this, 'add_body_class' ] );
	}

	/**
	 * Заполняет свойство "options" данными (настройками модуля).
	 *
	 * @return void
	 */
	public function set_properties() {
		$options = get_option( 'hide_admin_menu_items' );

		$this->options = $options && is_array( $options ) ? $options : [];
		$this->items   = ! empty( $this->options['items'] ) ? $this->options['items'] : [];
		$this->status  = ! empty( $this->options['status'] );
	}

	/**
	 * Сохраняет опции плагина.
	 *
	 * @return void
	 */
	public function save_options() {
		update_option( 'hide_admin_menu_items', $this->options, false );
	}

	/**
	 * Добавляет к css классам тега body класс-статус плагина.
	 *
	 * @param string $classes
	 *
	 * @return  string $classes
	 */
	public function add_body_class( $classes ) {
		return $this->status || empty( $this->items ) ? $classes . 'hami-enable' : $classes;
	}

	/**
	 * Добавляет переключатель в админ-бар.
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
	 * Возвращает html блок с переключателем.
	 *
	 * @return string
	 */
	public function get_switch_html() {
		ob_start();
		?>

        <form class="switch__container">
            <input id="switch-flat" class="switch" type="checkbox"<?php checked( $this->status ); ?>>
            <label for="switch-flat" title="Скройте или отобразите выбранные пункты меню"></label>
            <div class="switch__content">
				<?php
				if ( ! empty( $this->items ) ) {
					foreach ( $this->items as $id => $text ) {
						printf( '<p data-id="%s">
                                    <span class="text">%s</span>
                                    <span class="dashicons dashicons-no hami-restore-li" title="Убрать из списка"></span>
                                </p>', $id, $text );
					}
				} else {
					echo '<p class="no-items">Нет скрытых пунктов меню</p>';
				}
				?>
            </div>
        </form>

		<?php
		return ob_get_clean();
	}

	/**
	 * Выводит на экран CSS и JavaScript.
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
			'no_items'    => 'Нет скрытых пунктов меню',
			'count_items' => count( $this->items ),
		] );
	}

	/**
	 * Добавляет на вывод CSS стили для скрытия пунктов меню.
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
	 * Возвращает опции из суперглобального массива POST.
	 *
	 * @param string $key Ключ данных.
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
	 * Регистрирует AJAX обработчики.
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
	 * Сохраняет статус приложения.
	 *
	 * @return void
	 */
	public function ajax_save_status() {
		$this->check_ajax_referer();

		$status = $this->get_ajax_options( 'status' );

		if ( $status === '1' || $status === '0' ) {
			$this->options['status'] = $status;
			$this->save_options();
		}

		wp_die();
	}

	/**
	 * Сохраняет данные о пункте меню, который нужно скрыть.
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
	 * Удаляет данные о пункте меню, который нужно скрыть.
	 *
	 * @return void
	 */
	public function ajax_remove_item() {
		$this->check_ajax_referer();

		$id = $this->get_ajax_options( 'id' );
		unset( $this->options['items'][ $id ] );

		if ( $this->status ) {
			$this->options['status'] = $this->options['items'] && is_array( $this->options['items'] );
		}

		$this->save_options();

		wp_die();
	}

	/**
	 * Проверяет Ajax запрос, на соответствие nonce коду.
	 *
	 * @see check_ajax_referer()
	 *
	 * @return void
	 */
	public function check_ajax_referer() {
		check_ajax_referer( 'hami-nonce' );
	}
}