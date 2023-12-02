<?php
/**
 * Uninstalling options.
 */
defined( 'WP_UNINSTALL_PLUGIN' ) || exit;

// Delete options from the main site
delete_option('cb87_hide_admin_menu_items');

// Delete options from all sites in the network for multisite
if (is_multisite()) {
    global $wpdb;
    $blog_ids = $wpdb->get_col("SELECT blog_id FROM {$wpdb->blogs}");
    foreach ($blog_ids as $blog_id) {
        switch_to_blog($blog_id);
        delete_option('cb87_hide_admin_menu_items');
        restore_current_blog();
    }
}

// Delete user options from all sites in the network for multisite
if (is_multisite()) {
    global $wpdb;
    $user_ids = $wpdb->get_col("SELECT user_id FROM {$wpdb->usermeta} WHERE meta_key = 'cb87_hide_admin_menu_users'");
    foreach ($user_ids as $user_id) {
        delete_user_meta($user_id, 'cb87_hide_admin_menu_users');
    }
}
