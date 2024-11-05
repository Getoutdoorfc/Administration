<?php
/* class-administration-admin */

if (!defined('ABSPATH')) {
    exit; // Stop direkte adgang
}

class Administration_Admin {
    private $plugin_name;
    private $version;

    public function __construct($plugin_name, $version) {
        $this->plugin_name = $plugin_name;
        $this->version = $version;
    }

    public function enqueue_styles() {
        $style_path = plugin_dir_path(__FILE__) . 'css/administration-admin.css';
        $style_url = plugin_dir_url(__FILE__) . 'css/administration-admin.css';
        $version = filemtime($style_path);

        wp_enqueue_style($this->plugin_name . '-admin', $style_url, array(), $version, 'all');
        error_log("Admin style enqueued: {$style_url} (version: {$version})");
    }

    public function enqueue_scripts() {
        $script_path = plugin_dir_path(__FILE__) . 'js/administration-admin.js';
        $script_url = plugin_dir_url(__FILE__) . 'js/administration-admin.js';
        $version = filemtime($script_path);

        wp_enqueue_script($this->plugin_name . '-admin', $script_url, array('jquery'), $version, true);
        error_log("Admin script enqueued: {$script_url} (version: {$version})");
    }

    public function display_admin_dashboard() {
        ?>
        <div class="wrap">
            <h1><?php _e('Administration Dashboard', 'administration'); ?></h1>
            <p><?php _e('Velkommen til Administration pluginet.', 'administration'); ?></p>
            <form method="post" action="options.php">
                <?php
                settings_fields('administration_microsoft_settings');
                do_settings_sections('administration_microsoft_settings');
                submit_button();
                ?>
            </form>
        </div>
        <?php
    }
}
