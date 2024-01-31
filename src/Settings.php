<?php

namespace EHAMI;

/**
 * @property-read array $items
 * @property-read bool  $status
 * @property-read bool  $save_individually
 * @property-read bool  $hide_submenu
 * @property-read bool  $remove_all_data_on_uninstall
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
			$this->options['items'] = get_user_meta( get_current_user_id(), self::USER_ITEMS_META_KEY, true ) ?: [];
		}
	}

	public function get_db_options(): array {
		return get_option( self::OPTION_NAME, [] );
	}

	public function save_options( array $new_options ) {
		$options = $this->sanitize_options( $new_options );

		update_option( self::OPTION_NAME, $options, false );

		if ( $options['save_individually'] && $options['items'] ) {
			update_user_meta( get_current_user_id(), self::USER_ITEMS_META_KEY, wp_slash( $options['items'] ) );
		}
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

		if ( isset( $new_options['items'] ) && is_array( $new_options['items'] ) ) {
			$options['items'] = array_map( 'sanitize_text_field', $new_options['items'] );
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
