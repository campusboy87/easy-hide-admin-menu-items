<?php
/**
 * Plugin Name: CB87 Hide Admin Menu Items
 * Plugin URI: https://github.com/campusboy87/hide-admin-menu-items
 * Text Domain: cb87-hami
 * Domain Path: /languages
 * Description: The plugin allows hiding selected menu items.
 * Version: 1.3
 * Author: Campusboy, Dan Zakirov
 * Author URI: https://wp-plus.ru/
 * License: GPL-2.0+
 * License URI:  https://www.gnu.org/licenses/gpl-2.0.html
 * Requires PHP: 5.6
 * Requires at least: 4.2.0
 * Tested up to: 6.4
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 */

defined( 'ABSPATH' ) || exit;

/**
 * Initializes the plugin.
 *
 * @return void
 */
function cb87_hami_init() {
    load_plugin_textdomain( 'cb87-hami', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );

    if ( is_admin() && current_user_can( 'manage_options' ) ) {
        require_once __DIR__ . DIRECTORY_SEPARATOR . 'inc/cb87-class-hami.php';
        ( new CB87_Hide_Admin_Menu_Items() )->init();
    }
}

// Hook the initialization function to the 'plugins_loaded' action.
add_action( 'plugins_loaded', 'cb87_hami_init' );