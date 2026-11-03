<?php

namespace EHAMI;

/**
 * @property-read array  $items
 * @property-read bool   $status
 * @property-read bool   $save_individually
 * @property-read bool   $hide_submenu
 * @property-read bool   $remove_all_data_on_uninstall
 * @property-read bool   $hide_icons_disable
 * @property-read string $separators_mode
 * @property-read bool   $enable_menu_sorting
 * @property-read array  $menu_order
 * @property-read bool   $enable_sidebar_resize
 * @property-read int    $sidebar_width
 */
class Settings {

	const OPTION_NAME = 'easy_hide_admin_menu_items_plugin_settings';
	const USER_ITEMS_META_KEY = 'ehami_hidden_menu_items';

	const DEFAULT_OPTIONS = [
		'items'                        => [],
		'status'                       => true,
		'save_individually'            => false,
		'hide_submenu'                 => false,
		'remove_all_data_on_uninstall' => false,
		'hide_icons_disable'           => false,
		// Experimental features.
		'enable_menu_sorting'          => false,
		'menu_order'                   => [],
		'separators_mode'              => 'default',
		'enable_sidebar_resize'        => false,
		'sidebar_width'                => 0,
	];

	/**
	 * @var array
	 */
	private $options;

	public function __construct() {
		$this->set_options();
	}

	public function set_options() {
		$this->options = array_merge( self::DEFAULT_OPTIONS, $this->get_db_options() );

		if ( $this->options['save_individually'] ) {
			if ( metadata_exists( 'user', get_current_user_id(), 'ehami_user_status' ) ) {
				$this->options['status'] = (bool) get_user_meta( get_current_user_id(), 'ehami_user_status', true );
			}
			$this->options['items']              = get_user_meta( get_current_user_id(), self::USER_ITEMS_META_KEY, true ) ?: [];
			$this->options['hide_icons_disable'] = get_user_meta( get_current_user_id(), 'hide_icons_disable', true ) ?: false;
			$this->options['menu_order']         = get_user_meta( get_current_user_id(), 'ehami_user_menu_order', true ) ?: [];
			$this->options['sidebar_width']      = (int) get_user_meta( get_current_user_id(), 'ehami_sidebar_width', true );
		}
	}

	public function get_db_options(): array {
		return get_option( self::OPTION_NAME, [] );
	}

	public function save_options( array $new_options ) {
		$options = $this->sanitize_options( $new_options );

		if ( $options['save_individually'] ) {
			$global_options = array_merge( self::DEFAULT_OPTIONS, $this->get_db_options() );
			$personal_keys  = [
				'status'             => 'ehami_user_status',
				'items'              => self::USER_ITEMS_META_KEY,
				'hide_icons_disable' => 'hide_icons_disable',
				'menu_order'         => 'ehami_user_menu_order',
				'sidebar_width'      => 'ehami_sidebar_width',
			];

			foreach ( $personal_keys as $key => $meta_key ) {
				if ( isset( $new_options[ $key ] ) && ( ! is_array( self::DEFAULT_OPTIONS[ $key ] ) || is_array( $new_options[ $key ] ) ) ) {
					$value = $key === 'items' ? wp_slash( $options[ $key ] ) : $options[ $key ];
					update_user_meta( get_current_user_id(), $meta_key, $value );
				}

				$options[ $key ] = $global_options[ $key ];
			}
		}

		update_option( self::OPTION_NAME, $options, false );
	}

	public function sanitize_options( array $new_options ): array {
		$options = array_merge( self::DEFAULT_OPTIONS, $this->get_db_options() );

		if ( isset( $new_options['status'] ) ) {
			$options['status'] = (bool) $new_options['status'];
		}

		if ( isset( $new_options['hide_submenu'] ) ) {
			$options['hide_submenu'] = (bool) $new_options['hide_submenu'];
		}

		if ( isset( $new_options['save_individually'] ) ) {
			$options['save_individually'] = (bool) $new_options['save_individually'];
		}

		if ( isset( $new_options['remove_all_data_on_uninstall'] ) ) {
			$options['remove_all_data_on_uninstall'] = (bool) $new_options['remove_all_data_on_uninstall'];
		}

		if ( isset( $new_options['hide_icons_disable'] ) ) {
			$options['hide_icons_disable'] = (bool) $new_options['hide_icons_disable'];
		}

		if ( isset( $new_options['items'] ) && is_array( $new_options['items'] ) ) {
			$options['items'] = array_map( 'sanitize_text_field', $new_options['items'] );
		}

		if ( isset( $new_options['enable_menu_sorting'] ) ) {
			$options['enable_menu_sorting'] = (bool) $new_options['enable_menu_sorting'];
		}

		if ( isset( $new_options['menu_order'] ) && is_array( $new_options['menu_order'] ) ) {
			$sanitized_order = [];
			foreach ( $new_options['menu_order'] as $selector => $position ) {
				$sanitized_order[ sanitize_text_field( $selector ) ] = absint( $position );
			}
			$options['menu_order'] = $sanitized_order;
		}

		if ( isset( $new_options['separators_mode'] ) ) {
			$allowed_modes = [ 'default', 'hidden', 'highlight' ];
			$mode          = sanitize_text_field( $new_options['separators_mode'] );
			$options['separators_mode'] = in_array( $mode, $allowed_modes, true ) ? $mode : 'default';
		}

		if ( isset( $new_options['enable_sidebar_resize'] ) ) {
			$options['enable_sidebar_resize'] = (bool) $new_options['enable_sidebar_resize'];
		}

		if ( isset( $new_options['sidebar_width'] ) ) {
			$width = absint( $new_options['sidebar_width'] );
			// Clamp to valid range: 0 (default/unset), or 160–480.
			if ( $width > 0 ) {
				$width = max( 160, min( 480, $width ) );
			}
			$options['sidebar_width'] = $width;
		}

		return $options;
	}

	public function add_menu_item( string $id, string $text ) {
		$this->save_options( [
			'items' => array_merge( $this->items, [ $id => $text ] ),
		] );
	}

	public function remove_menu_item( string $id ) {
		$items = $this->items;

		unset( $items[ $id ] );

		$this->save_options( [
			'items' => $items,
		] );
	}

	public function __get( string $name ) {
		return $this->options[ $name ] ?? null;
	}

	public function __set( string $name, $value ) {
		return null;
	}

	public function __isset( string $name ) {
		return ! is_null( $this->{$name} );
	}
}
