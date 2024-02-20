<?php
/*
Plugin Name: Black List - PRO
Description: This plugin helps in managing blacklisted customers.
Version: 1.0
Author: Sujith
Author URI: https://www.linkedin.com/in/sujith-balan-828530221/
*/
define( 'BLACKLIST_PRO_PLUGIN_FILE', __FILE__ );
require_once dirname( BLACKLIST_PRO_PLUGIN_FILE ) . '/admin/blacklist-pro-admin.php';
require_once dirname( BLACKLIST_PRO_PLUGIN_FILE ) . '/inc/db_setup.php';
register_activation_hook( BLACKLIST_PRO_PLUGIN_FILE, 'blacklist_pro_activate' );

function blacklist_pro_activate() {
    if (blacklist_pro_create_table()) {
        error_log('Database table created successfully.');
    } else {
        error_log('Failed to create database table.');
    }
}

function blacklist_pro_enqueue_styles() {
    wp_enqueue_style('bootstrap', plugins_url('css/bootstrap/css/bootstrap.min.css', __FILE__));
}
add_action('admin_enqueue_scripts', 'blacklist_pro_enqueue_styles');
