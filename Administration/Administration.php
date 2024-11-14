<?php
/*
Plugin Name: Administration
Description: Plugin for administration.
Version: 1.0.0
Author: Sune
Text Domain: administration
Domain Path: /languages
*/

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}

// Include Composer autoloader
require_once __DIR__ . '/vendor/autoload.php';

// Define plugin constants for directory paths and URLs
define( 'ADMINISTRATION_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
define( 'ADMINISTRATION_PLUGIN_URL', plugin_dir_url( __FILE__ ) );

// Import nødvendige klasser
use Administration\Components\Utilities\HtaccessHandler;
use Administration\Includes\Main;
use Administration\Components\Utilities\Logger;

/**
 * Register plugin activation and deactivation hooks.
 */
register_activation_hook( __FILE__, [ HtaccessHandler::class, 'updateHtaccess' ] );
register_deactivation_hook( __FILE__, [ HtaccessHandler::class, 'removeHtaccessRules' ] );

/**
 * Load plugin textdomain for translations.
 */
function administration_load_textdomain() {
    load_plugin_textdomain( 'administration', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );
}
add_action( 'plugins_loaded', 'administration_load_textdomain' );

/**
 * Check for WooCommerce dependency and show admin notice if not present.
 */
function administration_check_dependencies() {
    // Tjek om WooCommerce er installeret og aktivt
    $woocommerce_installed = class_exists( 'WooCommerce' );

    if ( ! $woocommerce_installed ) {
        add_action( 'admin_notices', 'administration_dependency_notice' );
    }
}
add_action( 'plugins_loaded', 'administration_check_dependencies' );

/**
 * Display an admin notice for missing WooCommerce plugin.
 */
function administration_dependency_notice() {
    ?>
    <div class="notice notice-warning is-dismissible">
        <p><?php esc_html_e( 'WooCommerce er ikke installeret. Administration-pluginet fungerer bedst med WooCommerce.', 'administration' ); ?></p>
    </div>
    <?php
}

/**
 * Enqueue admin scripts and styles.
 */
function administration_enqueue_admin_scripts() {
    wp_enqueue_style( 'administration-admin-css', ADMINISTRATION_PLUGIN_URL . 'assets/css/admin.css', [], '1.0.0' );
    wp_enqueue_script( 'administration-admin-js', ADMINISTRATION_PLUGIN_URL . 'assets/js/admin.js', [ 'jquery' ], '1.0.0', true );
}
add_action( 'admin_enqueue_scripts', 'administration_enqueue_admin_scripts' );

/**
 * Enqueue frontend scripts and styles.
 */
function administration_enqueue_frontend_scripts() {
    wp_enqueue_style( 'administration-frontend-css', ADMINISTRATION_PLUGIN_URL . 'assets/css/frontend.css', [], '1.0.0' );
    wp_enqueue_script( 'administration-frontend-js', ADMINISTRATION_PLUGIN_URL . 'assets/js/frontend.js', [ 'jquery' ], '1.0.0', true );
}
add_action( 'wp_enqueue_scripts', 'administration_enqueue_frontend_scripts' );

/**
 * Run the main plugin class.
 */
function administration_run_plugin() {
    if ( class_exists( Main::class ) ) {
        $plugin = new Main();
        $plugin->run();
    } else {
        // Håndter fejlen: Main-klassen findes ikke
        if ( class_exists( Logger::class ) ) {
            Logger::getInstance()->error( 'Administration Plugin Error: Main class does not exist.' );
        } else {
            error_log( 'Administration Plugin Error: Main class does not exist.' );
        }
    }
}
add_action( 'plugins_loaded', 'administration_run_plugin' );
