<?php
/*
Plugin Name: Administration
Description: Et administrativt plugin til WordPress
Version: 1.0.0
Author: Sune
Text Domain: administration
Domain Path: /languages
*/

if (!defined('ABSPATH')) {
    exit; // Stop direkte adgang
}

define('ADMINISTRATION_PLUGIN_DIR', plugin_dir_path(__FILE__));
define('ADMINISTRATION_PLUGIN_URL', plugin_dir_url(__FILE__));

// Inkluder nødvendige klasser
require_once ADMINISTRATION_PLUGIN_DIR . 'includes/class-administration.php';
require_once ADMINISTRATION_PLUGIN_DIR . 'includes/class-administration-loader.php';
require_once ADMINISTRATION_PLUGIN_DIR . 'includes/class-administration-i18n.php';
require_once ADMINISTRATION_PLUGIN_DIR . 'includes/class-administration-admin.php';
require_once ADMINISTRATION_PLUGIN_DIR . 'includes/class-administration-public.php';
require_once ADMINISTRATION_PLUGIN_DIR . 'includes/class-administration-msgraph-auth.php';
require_once ADMINISTRATION_PLUGIN_DIR . 'includes/class-administration-order-handler.php';
require_once ADMINISTRATION_PLUGIN_DIR . 'includes/class-administration-settings.php';
require_once ADMINISTRATION_PLUGIN_DIR . 'includes/class-administration-oplevelse.php';

register_activation_hook(__FILE__, 'administration_activate');
add_action('admin_init', 'administration_check_setup');

function administration_activate() {
    // Redirect to authorization URL after activation
    $auth = new Administration_MSGraph_Auth();
    wp_redirect($auth->get_authorization_url());
    exit;
}

function administration_check_setup() {
    if (!get_transient('administration_access_token')) {
        // Redirect to authorization URL if not authenticated
        $auth = new Administration_MSGraph_Auth();
        wp_redirect($auth->get_authorization_url());
        exit;
    }
}

register_deactivation_hook(__FILE__, array('Administration', 'deactivate_plugin'));
register_uninstall_hook(__FILE__, array('Administration', 'uninstall_plugin'));

function run_administration() {
    error_log('run_administration() called');
    $plugin_dir = plugin_dir_path(__FILE__);
    error_log('Plugin directory: ' . $plugin_dir);

    $loader = new Administration_Loader();
    $admin = new Administration_Admin('administration', '1.0.0');
    $public = new Administration_Public('administration', '1.0.0');
    $msgraph_auth = new Administration_MSGraph_Auth();
    $order_handler = new Administration_Order_Handler();
    $settings = new Administration_Settings();
    $oplevelse = new Administration_Oplevelse();
    $i18n = new Administration_i18n('administration'); // Tilføj det nødvendige argument
    $i18n->load_plugin_textdomain();

    $plugin = Administration::get_instance($loader, $admin, $public, $msgraph_auth, $order_handler, $settings);
    $plugin->run();
}

add_action('plugins_loaded', 'run_administration');