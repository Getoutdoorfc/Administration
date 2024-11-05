<?php
/* class-administration */

if (!defined('ABSPATH')) {
    exit; // Stop direkte adgang
}

class Administration {
    private static $instance = null;
    private $loader;
    private $admin;
    private $public;
    private $msgraph_auth;
    private $order_handler;
    private $settings;

    private function __construct($loader, $admin, $public, $msgraph_auth, $order_handler, $settings) {
        $this->loader = $loader;
        $this->admin = $admin;
        $this->public = $public;
        $this->msgraph_auth = $msgraph_auth;
        $this->order_handler = $order_handler;
        $this->settings = $settings;
    }

    public static function get_instance($loader, $admin, $public, $msgraph_auth, $order_handler, $settings) {
        if (self::$instance == null) {
            self::$instance = new self($loader, $admin, $public, $msgraph_auth, $order_handler, $settings);
        }
        return self::$instance;
    }

    public static function activate_plugin() {
        flush_rewrite_rules();
    }

    public static function deactivate_plugin() {
        flush_rewrite_rules();
    }

    public static function uninstall_plugin() {
        delete_option('administration_default_setting');
        delete_option('administration_client_id');
        delete_option('administration_client_secret');
        delete_option('administration_tenant_id');
    }

    public function run() {
        $this->loader->add_action('admin_menu', $this, 'add_admin_menu');
        $this->loader->add_action('admin_enqueue_scripts', $this->admin, 'enqueue_styles');
        $this->loader->add_action('admin_enqueue_scripts', $this->admin, 'enqueue_scripts');
        $this->loader->add_action('wp_enqueue_scripts', $this->public, 'enqueue_styles');
        $this->loader->add_action('wp_enqueue_scripts', $this->public, 'enqueue_scripts');
        
        // Tilføj nye hooks for delta forespørgsler og cache-control integration
        $this->loader->add_action('woocommerce_order_status_changed', $this->order_handler, 'handle_order_status_change');
        
        $this->loader->run();
    }

    public function add_admin_menu() {
        // Tilføj indstillingssiden som undermenu via Administration_Settings
        if (method_exists($this->settings, 'add_admin_menu')) {
            $this->settings->add_admin_menu();
        }
    }

    public function display_admin_dashboard() {
        include_once ADMINISTRATION_PLUGIN_DIR . 'admin/partials/administration-admin-display.php';
    }
}
?>
