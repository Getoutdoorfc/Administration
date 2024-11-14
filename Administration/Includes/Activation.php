<?php
namespace Administration\Includes;

defined( 'ABSPATH' ) || exit;

/**
 * Class Activation
 *
 * Håndterer aktiveringslogik for pluginet.
 *
 * @package Administration\Includes
 */
class Activation {

    /**
     * Kører ved aktivering af pluginet.
     */
    public static function activate() {
        // Sæt standardindstillinger eller opret nødvendige tabeller i databasen.
        self::createRequiredTables();
    }

    /**
     * Opretter nødvendige tabeller i databasen.
     */
    private static function createRequiredTables() {
        global $wpdb;

        if ( ! function_exists( 'dbDelta' ) ) {
            require_once ABSPATH . 'wp-admin/includes/upgrade.php';
        }

        $table_name      = $wpdb->prefix . 'administration_example_table';
        $charset_collate = $wpdb->get_charset_collate();

        $sql = "CREATE TABLE IF NOT EXISTS $table_name (
            id mediumint(9) NOT NULL AUTO_INCREMENT,
            example_column varchar(100) NOT NULL,
            PRIMARY KEY  (id)
        ) $charset_collate;";

        require_once ABSPATH . 'wp-admin/includes/upgrade.php';
        dbDelta( $sql );
    }
}
