<?php
/**
 * Uninstalling options.
 */
defined( 'WP_UNINSTALL_PLUGIN' ) || exit;

delete_option( 'hide_admin_menu_items' );
