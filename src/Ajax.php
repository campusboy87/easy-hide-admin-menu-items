<?php

namespace EHAMI;

class Ajax {

	const NONCE_KEY = 'ehami-nonce';

	public function init() {
		add_action( 'wp_ajax_ehami_save_status', [ $this, 'action_save_status_to_db' ] );
		add_action( 'wp_ajax_ehami_save_settings', [ $this, 'action_save_settings_to_db' ] );
		add_action( 'wp_ajax_ehami_add_item', [ $this, 'action_add_item_to_db' ] );
		add_action( 'wp_ajax_ehami_remove_item', [ $this, 'action_remove_item_from_db' ] );
	}

	public function action_save_status_to_db() {
		check_ajax_referer( self::NONCE_KEY );

		if ( ! current_user_can( 'read' ) ) {
			wp_die(
				esc_html__( 'Sorry, you are not allowed to manage options for this site.' ),
				403
			);
		}

		plugin()->settings->save_options( [
			'status' => (bool) ( $_POST['options']['status'] ?? false ),
		] );

		wp_die();
	}

	public function action_save_settings_to_db() {
		check_ajax_referer( self::NONCE_KEY );

		if ( ! current_user_can( 'manage_options' ) ) {
			wp_die(
				esc_html__( 'Sorry, you are not allowed to manage options for this site.' ),
				403
			);
		}

		plugin()->settings->save_options( [
			'save_individually'            => (bool) ( $_POST['options']['save_individually'] ?? false ),
			'hide_submenu'                 => (bool) ( $_POST['options']['hide_submenu'] ?? false ),
			'remove_all_data_on_uninstall' => (bool) ( $_POST['options']['remove_all_data_on_uninstall'] ?? false ),
		] );

		wp_die();
	}

	public function action_add_item_to_db() {
		check_ajax_referer( self::NONCE_KEY );

		if ( ! current_user_can( 'read' ) ) {
			wp_die(
				esc_html__( 'Sorry, you are not allowed to manage options for this site.' ),
				403
			);
		}

		$id   = sanitize_text_field( wp_unslash( $_POST['options']['items']['id'] ?? '' ) );
		$text = sanitize_text_field( wp_unslash( $_POST['options']['items']['text'] ?? '' ) );

		if ( $id && $text ) {
			plugin()->settings->add_menu_item( $id, $text );
		}

		wp_die();
	}

	public function action_remove_item_from_db() {
		check_ajax_referer( self::NONCE_KEY );

		if ( ! current_user_can( 'read' ) ) {
			wp_die(
				esc_html__( 'Sorry, you are not allowed to manage options for this site.' ),
				403
			);
		}

		$id = (int) ( $_POST['options']['id'] ?? 0 );

		if ( $id ) {
			plugin()->settings->remove_menu_item( $id );
		}

		wp_die();
	}

}
