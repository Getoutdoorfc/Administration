<?php
/* class-administration-public */

if (!defined('ABSPATH')) {
    exit; // Stop direkte adgang
}

class Administration_Public {
    private $plugin_name;
    private $version;

    public function __construct($plugin_name, $version) {
        $this->plugin_name = $plugin_name;
        $this->version = $version;
    }

    public function enqueue_styles() {
        $style_path = ADMINISTRATION_PLUGIN_DIR . 'includes/css/administration-public.css';
        $style_url = ADMINISTRATION_PLUGIN_URL . 'includes/css/administration-public.css';
        if (file_exists($style_path)) {
            $version = filemtime($style_path);
            wp_enqueue_style($this->plugin_name . '-public', $style_url, array(), $version, 'all');
            error_log("Public style enqueued: {$style_url} (version: {$version})");
        } else {
            error_log("Public style file not found: {$style_path}");
        }
    }

    public function enqueue_scripts() {
        $script_path = ADMINISTRATION_PLUGIN_DIR . 'includes/js/administration-public.js';
        $script_url = ADMINISTRATION_PLUGIN_URL . 'includes/js/administration-public.js';
        if (file_exists($script_path)) {
            $version = filemtime($script_path);
            wp_enqueue_script($this->plugin_name . '-public', $script_url, array('jquery'), $version, true);
            error_log("Public script enqueued: {$script_url} (version: {$version})");
        } else {
            error_log("Public script file not found: {$script_path}");
        }
    }
}