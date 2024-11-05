<?php
/* class-administration-i18n */

if (!defined('ABSPATH')) {
    exit; // Stop direkte adgang
}

class Administration_i18n {
    private $domain;

    public function __construct($domain) {
        $this->domain = $domain;
        add_action('plugins_loaded', array($this, 'load_plugin_textdomain'));
    }

    public function load_plugin_textdomain() {
        $loaded = load_plugin_textdomain(
            $this->domain,
            false,
            dirname(plugin_basename(__FILE__)) . '/languages/'
        );

        if ($loaded) {
            error_log("Text domain '{$this->domain}' loaded successfully.");
        } else {
            error_log("Failed to load text domain '{$this->domain}'.");
        }
    }
}
