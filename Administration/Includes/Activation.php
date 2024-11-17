<?php
namespace Administration\Includes;

defined( 'ABSPATH' ) || exit;

class Activation {

    public static function activate() {
        self::createRequiredTables();
    }

    private static function createRequiredTables() {
        global $wpdb;

        if ( ! function_exists( 'dbDelta' ) ) {
            require_once ABSPATH . 'wp-admin/includes/upgrade.php';
        }

        $table_name      = $wpdb->prefix . 'administration_example_table';
        $charset_collate = $wpdb->get_charset_collate();

        $sql = "CREATE TABLE $table_name (
            id mediumint(9) NOT NULL AUTO_INCREMENT,
            example_column varchar(100) NOT NULL,
            PRIMARY KEY  (id)
        ) $charset_collate;";

        $result = dbDelta( $sql );

        if ( empty( $result ) ) {
            error_log( "Failed to create or update table: $table_name" );
        }
    }
}
