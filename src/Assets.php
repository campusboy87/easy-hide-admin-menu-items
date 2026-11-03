<?php

namespace EHAMI;

class Assets {

	public function init() {
		add_action( 'admin_enqueue_scripts', [ $this, 'enqueue_assets' ] );
	}

	public function enqueue_assets() {
		$version = plugin()->info['version'];
		$url     = plugin()->url;

		wp_enqueue_style( 'ehami-style', $url . '/assets/css/style.css', [], $version );
		$this->add_inline_style( 'ehami-style' );

		// SortableJS — loaded separately because it's a vendor library.
		wp_enqueue_script( 'sortablejs', $url . '/assets/js/vendor/Sortable.min.js', [], '1.15.7', true );

		// Main script — all EHAMI modules in one file.
		wp_enqueue_script( 'ehami', $url . '/assets/js/ehami.js', [ 'sortablejs' ], $version, true );
		wp_add_inline_script( 'ehami', 'var ehami = ' . wp_json_encode( $this->get_script_data() ), 'before' );
	}

	/**
	 * Build the data object passed to all JS modules via the `ehami` global.
	 *
	 * @return array
	 */
	private function get_script_data(): array {
		return [
			'nonce'                 => wp_create_nonce( 'ehami-nonce' ),
			'no_items'              => __( 'No hidden menu items', 'ehami' ),
			'hid'                   => __( 'Hide menu', 'ehami' ),
			'count_items'           => count( plugin()->settings->items ),
			'hide_submenu'          => plugin()->settings->hide_submenu,
			'saved'                 => __( 'Settings saved.', 'ehami' ),
			'save_error'            => __( 'Error saving settings.', 'ehami' ),
			'enable_menu_sorting'   => plugin()->settings->enable_menu_sorting,
			'can_customize'         => plugin()->settings->save_individually || current_user_can( 'manage_options' ),
			'menu_order'            => plugin()->settings->menu_order,
			'enable_sidebar_resize' => plugin()->settings->enable_sidebar_resize,
			'sidebar_width'         => plugin()->settings->sidebar_width,
			'video_url'             => plugin()->url . '/assets/mp4/',
		];
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

		// Sidebar width override.
		// Apply only to the expanded desktop menu; WordPress folds it on narrower screens.
		$sidebar_width = plugin()->settings->sidebar_width;
		if ( plugin()->settings->enable_sidebar_resize && $sidebar_width > 0 ) {
			$w = (int) $sidebar_width;
			$css .= sprintf(
				'@media (min-width: 961px) {' .
				'body:not(.folded) #adminmenuwrap, body:not(.folded) #adminmenuback, body:not(.folded) #adminmenu { width: %1$dpx; }' .
				'body:not(.folded) #wpcontent, body:not(.folded) #wpfooter { margin-left: %1$dpx; }' .
				'body:not(.folded) #adminmenu .wp-not-current-submenu .wp-submenu { left: %1$dpx; }' .
				'body:not(.folded) #adminmenu .wp-has-current-submenu .wp-submenu { width: 100%%; }' .
				'}',
				$w
			);
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
