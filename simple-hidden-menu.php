<?php
/**
 * Plugin Name: Simple Hidden Menu
 * Plugin URI: https://github.com/campusboy87/simple-hidden-menu
 * Description: Плагин позволяет скрывать выбранные пункты меню.
 * Version: 1.0
 * Author: campusboy
 * Author URI: https://wp-plus.ru/
 * License: GPL2
 * License URI:  https://www.gnu.org/licenses/gpl-2.0.html
 * Requires PHP: 5.4
 * Requires at least: 4.2.0
 * Tested up to: 4.9.5
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

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}


/**
 * Инициализирует плагин.
 *
 * @return void
 */
function shm_init() {
	if ( is_admin() && current_user_can( 'manage_options' ) ) {
		require __DIR__ . DIRECTORY_SEPARATOR . 'class-simple-hidden-menu.php';

		$plugin = new Simple_Hidden_Menu;
		$plugin->init();
	}
}

add_action( 'plugins_loaded', 'shm_init' );