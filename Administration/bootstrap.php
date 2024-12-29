<?php

// Sikrer, at filen ikke kan tilgås direkte.
if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly.
}

// Inkluderer Composer autoloader.
require_once __DIR__ . '/vendor/autoload.php';

// Definerer plugin-konstanter.
define( 'ADMINISTRATION_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
define( 'ADMINISTRATION_PLUGIN_URL', plugin_dir_url( __FILE__ ) );

// Brug nødvendige namespaces.
use Administration\Includes\Main;
use Administration\Core\Managers\LoggerManager;

/**
 * Initialiserer pluginet ved at køre den primære klasse.
 */
function administration_initialize_plugin() {
    if ( class_exists( Main::class ) ) {
        try {
            $plugin = new Main();
            $plugin->run();
        } catch ( Exception $e ) {
            if ( class_exists( LoggerManager::class ) ) {
                LoggerManager::getInstance()->error( 'Plugin initialization failed: ' . $e->getMessage(), [
                    'trace' => $e->getTraceAsString(),
                ]);
            }
        }
    } else {
        // Håndter fejl, hvis Main-klassen ikke findes.
        if ( class_exists( LoggerManager::class ) ) {
            LoggerManager::getInstance()->error( 'Plugin initialization failed: Main class not found.' );
        }
    }
}

// Hook til WordPress "plugins_loaded".
add_action( 'plugins_loaded', 'administration_initialize_plugin' );
