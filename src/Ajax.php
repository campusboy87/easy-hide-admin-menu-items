<?php

namespace EHAMI;

class Ajax {

	public function init() {
		add_action( 'wp_ajax_ehami_save_status', [ $this, 'action_save_status_to_db' ] );
		add_action( 'wp_ajax_ehami_save_settings', [ $this, 'action_save_settings_to_db' ] );
		add_action( 'wp_ajax_ehami_add_item', [ $this, 'action_add_item_to_db' ] );
		add_action( 'wp_ajax_ehami_remove_item', [ $this, 'action_remove_item_to_db' ] );
	}

	public function action_save_status_to_db() {
		$this->check_ajax_referer();

		plugin()->settings->save_options( [
			'status' => $this->get_input_option( 'status' ),
		] );

		wp_die();
	}

	public function action_save_settings_to_db() {
		$this->check_ajax_referer();

		plugin()->settings->save_options( [
			'save_individually'            => $this->get_input_option( 'save_individually' ),
			'hide_submenu'                 => $this->get_input_option( 'hide_submenu' ),
			'remove_all_data_on_uninstall' => $this->get_input_option( 'remove_all_data_on_uninstall' ),
		] );

		wp_die();
	}

	public function action_add_item_to_db() {
		$this->check_ajax_referer();

		$data = $this->get_input_option( 'item' );
		$id   = $data['id'] ?? null;
		$text = $data['text'] ?? null;

		if ( $id && $text ) {
			plugin()->settings->add_menu_item( $id, $text );
		}

		wp_die();
	}

	public function action_remove_item_to_db() {
		$this->check_ajax_referer();

		$id = $this->get_input_option( 'id' );

		if ( $id ) {
			plugin()->settings->remove_menu_item( $id );
		}

		wp_die();
	}

	public function check_ajax_referer() {
		check_ajax_referer( 'ehami-nonce' );
	}

	private function get_input_option( string $key ) {
		return $_POST['options'][ $key ] ?? null;
	}

}