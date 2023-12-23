<?php

namespace EHAMI;

use WP_Admin_Bar;

class Switcher {

	public function init() {
		add_action( 'admin_bar_menu', [ $this, 'add_to_admin_bar' ] );
		add_filter( 'admin_body_class', [ $this, 'add_admin_body_class' ] );
	}

	public function add_to_admin_bar( WP_Admin_Bar $wp_admin_bar ) {
		$wp_admin_bar->add_menu( [
			'id'     => 'ehami-switch',
			'parent' => 'top-secondary',
			'title'  => $this->get_html(),
		] );
	}

	public function get_html(): string {
		ob_start();

		include plugin()->path . '/templates/switcher.php';

		return ob_get_clean();
	}

	public function add_admin_body_class( string $classes ): string {
		return plugin()->settings->status ? $classes . ' ehami-enable' : $classes;
	}

}