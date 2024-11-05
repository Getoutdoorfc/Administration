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
        $style_path = plugin_dir_path(__FILE__) . 'css/administration-public.css';
        $style_url = plugin_dir_url(__FILE__) . 'css/administration-public.css';
        $version = filemtime($style_path);

        wp_enqueue_style($this->plugin_name . '-public', $style_url, array(), $version, 'all');
        error_log("Public style enqueued: {$style_url} (version: {$version})");
    }

    public function enqueue_scripts() {
        $script_path = plugin_dir_path(__FILE__) . 'js/administration-public.js';
        $script_url = plugin_dir_url(__FILE__) . 'js/administration-public.js';
        $version = filemtime($script_path);

        wp_enqueue_script($this->plugin_name . '-public', $script_url, array('jquery'), $version, true);
        error_log("Public script enqueued: {$script_url} (version: {$version})");
    }
}
