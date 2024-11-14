<?php
// Fil: includes/uninstall.php

if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
    exit;
}

require_once(ABSPATH . 'wp-load.php');
global $wpdb;

// Slet tabellen.
$table_name = $wpdb->prefix . 'administration_example_table';
$wpdb->query( "DROP TABLE IF EXISTS $table_name" );

// Slet gemte indstillinger.
delete_option( 'administration_plugin_option' );
delete_option( 'administration_microsoft_access_token' );
delete_option( 'administration_microsoft_refresh_token' );
delete_option( 'administration_microsoft_token_expires' );
