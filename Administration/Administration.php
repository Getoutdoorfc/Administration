<?php
/*
Plugin Name: Administration
Description: WordPress plugin for Microsoft-integration og WooCommerce håndtering.
Version: 1.0.0
Author: Sune
*/

defined('ABSPATH') || exit;

// Load Composer autoloader
require_once __DIR__ . '/vendor/autoload.php';

use Administration\Includes\Main;

add_action('plugins_loaded', function () {
    (new Main())->run();
});
