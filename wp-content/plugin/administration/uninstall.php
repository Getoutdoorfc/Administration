<?php

if (!defined('WP_UNINSTALL_PLUGIN')) {
    exit; // Stop direkte adgang
}

// Slet plugin-specifikke indstillinger
delete_option('administration_default_setting');
delete_option('administration_client_id');
delete_option('administration_client_secret');
delete_option('administration_tenant_id');

// Slet eventuelle brugerdefinerede post meta data
global $wpdb;
$wpdb->query("DELETE FROM {$wpdb->postmeta} WHERE meta_key LIKE '_administration_%'");

// Slet eventuelle brugerdefinerede term meta data
$wpdb->query("DELETE FROM {$wpdb->termmeta} WHERE meta_key LIKE '_administration_%'");

// Slet eventuelle brugerdefinerede user meta data
$wpdb->query("DELETE FROM {$wpdb->usermeta} WHERE meta_key LIKE '_administration_%'");

// Slet eventuelle brugerdefinerede options
$wpdb->query("DELETE FROM {$wpdb->options} WHERE option_name LIKE '_administration_%'");
