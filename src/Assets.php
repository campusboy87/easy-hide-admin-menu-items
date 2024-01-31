<?php

namespace EHAMI;

class Assets {

	public function init() {
		add_action( 'admin_enqueue_scripts', [ $this, 'enqueue_assets' ] );
	}

	public function enqueue_assets() {
		wp_enqueue_style( 'ehami-style', plugin()->url . '/assets/css/style.css', [], plugin()->info['version'] );
		$this->add_inline_style( 'ehami-style' );

		wp_enqueue_script(
			'ehami-user-interface',
			plugin()->url . '/assets/js/user-interface.js',
			[
				'jquery',
				'jquery-effects-transfer',
			],
			plugin()->info['version'],
			true
		);
		wp_enqueue_script( 'ehami-settings-page', plugin()->url . '/assets/js/settings-page.js', [ 'jquery' ], plugin()->info['version'], true );
		$data = [
			'nonce'        => wp_create_nonce( 'ehami-nonce' ),
			'no_items'     => __( 'No hidden menu items', 'ehami' ),
			'hid'          => __( 'Hide menu', 'ehami' ),
			'count_items'  => count( plugin()->settings->items ),
			'hide_submenu' => plugin()->settings->hide_submenu,
		];
		wp_add_inline_script( 'ehami-user-interface', 'const ehami = ' . wp_json_encode( $data ), 'before' );
	}

	public function add_inline_style( string $handle ) {
		$items = plugin()->settings->items;

		if ( ! $items ) {
			return;
		}

		$css = '';
		foreach ( $items as $id => $text ) {
			if ( str_contains( $id, '.php' ) ) {
				// for submenu items
				$css .= sprintf( '.ehami-enable #adminmenu .wp-submenu a[href="%s"] { display: none; }', esc_attr( $id ) );
			} elseif ( str_contains( $id, '#' ) ) {
				// for parent menu items
				$css .= sprintf( '.ehami-enable #adminmenu %s { display: none; }', esc_attr( $id ) );
			}
		}
		wp_add_inline_style( $handle, $css );
	}

}
