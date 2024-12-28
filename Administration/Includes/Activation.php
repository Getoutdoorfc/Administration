<?php
namespace Administration\Includes;

use Administration\Core\Managers\LoggerManager;

defined('ABSPATH') || exit;

/**
 * Class Activation
 *
 * Håndterer aktiveringsprocessen for pluginet, herunder:
 * - Oprettelse af nødvendige database-tabeller.
 * - Automatisk tilføjelse af Microsoft-konstanter til wp-config.php.
 * - Logning af aktiveringsstatus og potentielle fejl.
 */

class Activation {

    /**
     * Aktiver pluginet.
     */
    public static function activate() {
        self::createRequiredTables();
        self::add_constants_to_wp_config();
        LoggerManager::getInstance()->info('Plugin activated successfully.');
    }

    /**
     * Opret de nødvendige database-tabeller.
     */
    private static function createRequiredTables() {
        global $wpdb;

        if (!function_exists('dbDelta')) {
            require_once ABSPATH . 'wp-admin/includes/upgrade.php';
        }

        $table_name = $wpdb->prefix . 'administration_example_table';
        $charset_collate = $wpdb->get_charset_collate();

        $sql = "CREATE TABLE $table_name (
            id mediumint(9) NOT NULL AUTO_INCREMENT,
            example_column varchar(100) NOT NULL,
            PRIMARY KEY  (id)
        ) $charset_collate;";

        $result = dbDelta($sql);

        if (empty($result)) {
            LoggerManager::getInstance()->warning("Failed to create or update table: $table_name");
        } else {
            LoggerManager::getInstance()->info("Table $table_name created or updated successfully.");
        }
    }

    /**
     * Tilføjer Microsoft konstanter til wp-config.php, hvis de ikke allerede findes.
     */
    private static function add_constants_to_wp_config() {
        $config_file = ABSPATH . 'wp-config.php';

        if (!file_exists($config_file) || !is_writable($config_file)) {
            LoggerManager::getInstance()->warning('Unable to write to wp-config.php. Please ensure it is writable.');
            return;
        }

        $constants = [
            'MICROSOFT_CLIENT_ID' => 'your-client-id-here',
            'MICROSOFT_CLIENT_SECRET' => 'your-client-secret-here',
            'MICROSOFT_TENANT_ID' => 'your-tenant-id-here',
        ];

        $config_contents = file_get_contents($config_file);

        foreach ($constants as $key => $default_value) {
            if (!defined($key) && strpos($config_contents, "define('$key'") === false) {
                $define_statement = "define('$key', '$default_value');\n";
                $config_contents .= "\n// Added by Administration Plugin\n$define_statement";
                LoggerManager::getInstance()->info("$key added to wp-config.php with default value.");
            }
        }

        // Skriv de opdaterede indstillinger tilbage til wp-config.php
        file_put_contents($config_file, $config_contents);
    }
}
