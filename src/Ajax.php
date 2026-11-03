<?php

namespace EHAMI;

class Ajax {

	const NONCE_KEY = 'ehami-nonce';

	public function init() {
		add_action( 'wp_ajax_ehami_save_status', [ $this, 'action_save_status_to_db' ] );
		add_action( 'wp_ajax_ehami_save_settings', [ $this, 'action_save_settings_to_db' ] );
		add_action( 'wp_ajax_ehami_add_item', [ $this, 'action_add_item_to_db' ] );
		add_action( 'wp_ajax_ehami_remove_item', [ $this, 'action_remove_item_from_db' ] );
		add_action( 'wp_ajax_ehami_hide_icons_disable', [ $this, 'action_hide_icons_disable' ] );
		add_action( 'wp_ajax_ehami_save_menu_order', [ $this, 'action_save_menu_order' ] );
		add_action( 'wp_ajax_ehami_reset_menu_order', [ $this, 'action_reset_menu_order' ] );
		add_action( 'wp_ajax_ehami_save_sidebar_width', [ $this, 'action_save_sidebar_width' ] );
		add_action( 'wp_ajax_ehami_reset_sidebar_width', [ $this, 'action_reset_sidebar_width' ] );
	}

	public function action_save_status_to_db() {

		check_ajax_referer( self::NONCE_KEY );

		if ( ! current_user_can( plugin()->settings->save_individually ? 'read' : 'manage_options' ) ) {
			wp_die(
				esc_html__( 'Sorry, you are not allowed to manage options for this site.', 'ehami' ),
				403
			);
		}

		plugin()->settings->save_options( [
			'status' => (bool) sanitize_text_field( wp_unslash( $_POST['options']['status'] ?? false ) ),
		] );

		wp_die();
	}

	public function action_save_settings_to_db() {
		check_ajax_referer( self::NONCE_KEY );

		if ( ! current_user_can( 'manage_options' ) ) {
			wp_die(
				esc_html__( 'Sorry, you are not allowed to manage options for this site.', 'ehami' ),
				403
			);
		}

		$options = [
			'save_individually'            => (bool) sanitize_text_field( wp_unslash( $_POST['options']['save_individually'] ?? false ) ),
			'hide_submenu'                 => (bool) sanitize_text_field( wp_unslash( $_POST['options']['hide_submenu'] ?? false ) ),
			'remove_all_data_on_uninstall' => (bool) sanitize_text_field( wp_unslash( $_POST['options']['remove_all_data_on_uninstall'] ?? false ) ),
			'enable_menu_sorting'          => (bool) sanitize_text_field( wp_unslash( $_POST['options']['enable_menu_sorting'] ?? false ) ),
			'enable_sidebar_resize'        => (bool) sanitize_text_field( wp_unslash( $_POST['options']['enable_sidebar_resize'] ?? false ) ),
		];

		if ( isset( $_POST['options']['separators_mode'] ) ) {
			$options['separators_mode'] = sanitize_text_field( wp_unslash( $_POST['options']['separators_mode'] ) );
		}

		plugin()->settings->save_options( $options );

		wp_die();
	}

	public function action_add_item_to_db() {
		check_ajax_referer( self::NONCE_KEY );

		if ( ! current_user_can( plugin()->settings->save_individually ? 'read' : 'manage_options' ) ) {
			wp_die(
				esc_html__( 'Sorry, you are not allowed to manage options for this site.', 'ehami' ),
				403
			);
		}

		$id   = sanitize_text_field( wp_unslash( $_POST['options']['item']['id'] ?? '' ) );
		$text = sanitize_text_field( wp_unslash( $_POST['options']['item']['text'] ?? '' ) );

		if ( $id && $text ) {
			plugin()->settings->add_menu_item( $id, $text );
		}

		wp_die();
	}

	public function action_remove_item_from_db() {
		check_ajax_referer( self::NONCE_KEY );

		if ( ! current_user_can( plugin()->settings->save_individually ? 'read' : 'manage_options' ) ) {
			wp_die(
				esc_html__( 'Sorry, you are not allowed to manage options for this site.', 'ehami' ),
				403
			);
		}

		$id = sanitize_text_field( wp_unslash( $_POST['options']['id'] ?? '' ) );

		if ( $id ) {
			plugin()->settings->remove_menu_item( $id );
		}

		wp_die();
	}

	public function action_hide_icons_disable() {
		check_ajax_referer( self::NONCE_KEY );

		if ( ! current_user_can( plugin()->settings->save_individually ? 'read' : 'manage_options' ) ) {
			wp_die(
				esc_html__( 'Sorry, you are not allowed to manage options for this site.', 'ehami' ),
				403
			);
		}

		plugin()->settings->save_options( [
			'hide_icons_disable' => (bool) sanitize_text_field( wp_unslash( $_POST['options']['hide_icons_disable'] ?? false ) ),
		] );

		wp_die();
	}

	/**
	 * Save menu order via AJAX (called on drag-and-drop end).
	 *
	 * @since 1.4.0
	 */
	public function action_save_menu_order() {
		check_ajax_referer( self::NONCE_KEY );

		if ( ! current_user_can( plugin()->settings->save_individually ? 'read' : 'manage_options' ) ) {
			wp_die(
				esc_html__( 'Sorry, you are not allowed to manage options for this site.', 'ehami' ),
				403
			);
		}

		// Each selector and position is sanitized separately below.
		$order = isset( $_POST['order'] ) && is_array( $_POST['order'] ) ? wp_unslash( $_POST['order'] ) : []; // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized

		$sanitized = [];
		foreach ( $order as $selector => $position ) {
			$sanitized[ sanitize_text_field( $selector ) ] = absint( $position );
		}

		plugin()->settings->save_options( [
			'menu_order' => $sanitized,
		] );

		wp_die();
	}

	/**
	 * Reset menu order to default (called from settings page).
	 *
	 * @since 1.4.0
	 */
	public function action_reset_menu_order() {
		check_ajax_referer( self::NONCE_KEY );

		if ( ! current_user_can( plugin()->settings->save_individually ? 'read' : 'manage_options' ) ) {
			wp_die(
				esc_html__( 'Sorry, you are not allowed to manage options for this site.', 'ehami' ),
				403
			);
		}

		plugin()->settings->save_options( [
			'menu_order' => [],
		] );

		wp_send_json_success();
	}

	/**
	 * Save sidebar width via AJAX (called on resize end or range change).
	 *
	 * @since 1.4.0
	 */
	public function action_save_sidebar_width() {
		check_ajax_referer( self::NONCE_KEY );

		if ( ! current_user_can( plugin()->settings->save_individually ? 'read' : 'manage_options' ) ) {
			wp_die(
				esc_html__( 'Sorry, you are not allowed to manage options for this site.', 'ehami' ),
				403
			);
		}

		$width = isset( $_POST['width'] ) ? absint( $_POST['width'] ) : 0;

		plugin()->settings->save_options( [
			'sidebar_width' => $width,
		] );

		wp_die();
	}

	/**
	 * Reset sidebar width to default (called from settings page).
	 *
	 * @since 1.4.0
	 */
	public function action_reset_sidebar_width() {
		check_ajax_referer( self::NONCE_KEY );

		if ( ! current_user_can( plugin()->settings->save_individually ? 'read' : 'manage_options' ) ) {
			wp_die(
				esc_html__( 'Sorry, you are not allowed to manage options for this site.', 'ehami' ),
				403
			);
		}

		plugin()->settings->save_options( [
			'sidebar_width' => 0,
		] );

		wp_send_json_success();
	}
}
