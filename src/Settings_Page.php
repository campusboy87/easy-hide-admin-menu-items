<?php

namespace EHAMI;

class Settings_Page {

	const PARENT_SLUG = 'themes.php';

	public function init() {
		add_action( 'admin_menu', [ $this, 'add_settings_page' ] );
		add_filter( 'plugin_action_links', [ $this, 'add_settings_link_to_plugin_actions' ], 10, 2 );
	}

	public function add_settings_page() {
		add_submenu_page(
			self::PARENT_SLUG,
			__( 'Admin Menu Settings', 'ehami' ),
			__( 'Admin Menu Settings', 'ehami' ),
			'manage_options',
			'ehami-settings',
			[ $this, 'render' ]
		);
	}

	public function render() {
		require plugin()->path . '/templates/admin-page-options.php';
	}

	public function add_settings_link_to_plugin_actions( array $actions, string $plugin_basename ): array {
		if ( $plugin_basename !== plugin()->basename ) {
			return $actions;
		}

		$settings_link = sprintf(
			'<a href="%s">%s</a>',
			admin_url( '/' . self::PARENT_SLUG . '?page=ehami-settings' ),
			__( 'Settings', 'ehami' )
		);
		array_unshift( $actions, $settings_link );

		return $actions;
	}

}