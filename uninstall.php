<?php
// if uninstall.php is not called by WordPress, die
if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
    die;
}
// delete options set by plugin
delete_option( 'wp_param_to_cookie_variable');
delete_option( 'wp_param_to_cookie_time' );

// remove the database table created by the plugin
global $wpdb;
$table_name = $wpdb->prefix . 'wp_param_to_cookie_data';
$wpdb->query("DROP TABLE IF EXISTS $table_name");
