<?php
/**
 * Plugin Name: Easy Hide Admin Menu Items
 * Plugin URI: https://github.com/campusboy87/easy-hide-admin-menu-items
 * Text Domain: ehami
 * Domain Path: /languages
 * Description: The plugin allows hiding selected admin menu items.
 * Version: 1.3.6
 * Author: Campusboy, Dan Zakirov, Tkama
 * License: GPL-2.0+
 * License URI:  https://www.gnu.org/licenses/gpl-2.0.html
 * Requires PHP: 7.0
 * Requires at least: 5.9.0
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

namespace EHAMI;

defined( 'ABSPATH' ) || exit;

// The plugin functions exclusively in the admin area.
if ( ! is_admin() ) {
	return;
}

require_once __DIR__ . '/autoload.php';

add_action( 'init', [ plugin(), 'init' ] );

function plugin(): Plugin {
	static $plugin;

	if ( ! $plugin ) {
		$plugin = new Plugin( __FILE__ );
	}

	return $plugin;
}
