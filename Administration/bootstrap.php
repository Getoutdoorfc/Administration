<?php
// phpstan-bootstrap.php

// Definerer WordPress-konstanter, hvis de ikke allerede er defineret
if ( ! defined( 'ABSPATH' ) ) {
    define( 'ABSPATH', __DIR__ . '/' );
}

// Inkluderer Composer autoloader
require_once __DIR__ . '/vendor/autoload.php';

// Mock WordPress-funktioner, som PHPStan ellers ikke finder
if ( ! function_exists( 'add_action' ) ) {
    function add_action( $hook_name, $callback, $priority = 10, $accepted_args = 1 ) {
        // Dummy-implementering
    }
}

if ( ! function_exists( 'add_filter' ) ) {
    function add_filter( $hook_name, $callback, $priority = 10, $accepted_args = 1 ) {
        // Dummy-implementering
    }
}
