<?php

namespace EHAMI;

class Assets {

	public function init() {
		add_action( 'admin_enqueue_scripts', [ $this, 'enqueue_assets' ] );
	}

	public function enqueue_assets() {
		wp_enqueue_style( 'ehami-style', plugin()->url . '/assets/css/style.css', [], plugin()->info['version'] );
		$this->add_inline_style( 'ehami-style' );

		/**
		 * Load the user interface script only if the 'save_individually' option is set to true in the global settings,
		 * or if the current user is an administrator.
		 */
		if ( true === plugin()->settings->save_individually || current_user_can( 'administrator' ) ) {

			// SortableJS library for menu drag-and-drop reordering.
			wp_enqueue_script(
				'sortablejs',
				plugin()->url . '/assets/js/vendor/Sortable.min.js',
				[],
				'1.15.7',
				true
			);

			wp_enqueue_script(
				'ehami-user-interface',
				plugin()->url . '/assets/js/user-interface.js',
				[
					'jquery',
					'jquery-effects-transfer',
					'sortablejs',
				],
				plugin()->info['version'],
				true
			);

		}

		wp_enqueue_script( 'ehami-settings-page', plugin()->url . '/assets/js/settings-page.js', [ 'jquery' ], plugin()->info['version'], true );
		$data = [
			'nonce'               => wp_create_nonce( 'ehami-nonce' ),
			'no_items'            => __( 'No hidden menu items', 'ehami' ),
			'hid'                 => __( 'Hide menu', 'ehami' ),
			'count_items'         => count( plugin()->settings->items ),
			'hide_submenu'        => plugin()->settings->hide_submenu,
			'saved'               => __( 'Settings saved.', 'ehami' ),
			'save_error'          => __( 'Error saving settings.', 'ehami' ),
			'enable_menu_sorting' => true, // TODO: replace with setting from DB
		];
		wp_add_inline_script( 'ehami-user-interface', 'const ehami = ' . wp_json_encode( $data ), 'before' );

		wp_enqueue_script( 'ehami-experimental', plugin()->url . '/assets/js/experimental.js', [ 'jquery' ], plugin()->info['version'], true );
	}

	public function add_inline_style( string $handle ) {
		$css = '';

		// Hidden menu items.
		$items = plugin()->settings->items;
		if ( $items ) {
			foreach ( $items as $id => $text ) {
				if ( strpos( $id, '.php' ) !== false ) {
					// for submenu items
					$css .= sprintf( '.ehami-enable #adminmenu .wp-submenu a[href="%s"] { display: none; }', esc_attr( $id ) );
				} elseif ( strpos( $id, '#' ) !== false ) {
					// for parent menu items
					$css .= sprintf( '.ehami-enable #adminmenu %s { display: none; }', esc_attr( $id ) );
				}
			}
		}

		// Separators mode.
		$separators_mode = plugin()->settings->separators_mode;
		if ( $separators_mode === 'hidden' ) {
			$css .= '#adminmenu li.wp-menu-separator { display: none; }';
		} elseif ( $separators_mode === 'highlight' ) {
			$css .= '#adminmenu li.wp-menu-separator { height: auto; margin: 0; }';
			$css .= '#adminmenu li.wp-menu-separator > .separator { height: 2px; margin: 8px 12px; background: rgba(255,255,255,0.08); border: none; }';
		}

		if ( $css ) {
			wp_add_inline_style( $handle, $css );
		}
	}

}
