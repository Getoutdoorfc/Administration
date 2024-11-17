<?php
namespace Administration\Includes;

defined( 'ABSPATH' ) || exit;

/**
 * Class Database
 *
 * Håndterer databaseinteraktioner for pluginet.
 */
class Database {

    /**
     * Indsætter data i databasen.
     *
     * @param array $data Data der skal indsættes.
     */
    public static function insertData( $data ) {
        global $wpdb;
        $table_name = $wpdb->prefix . 'administration_example_table';

        // Sanitér data for at undgå sikkerhedsproblemer.
        $sanitized_data = array_map( 'sanitize_text_field', $data );

        if ( false === $wpdb->insert( $table_name, $sanitized_data ) ) {
            error_log( 'Database insert failed: ' . $wpdb->last_error );
        } else {
            error_log( 'Data inserted into table: ' . $table_name );
        }
    }

    /**
     * Henter data fra databasen.
     *
     * @param int $id ID for posten.
     * @return object|null Dataobjekt eller null, hvis ikke fundet.
     */
    public static function getData( $id ) {
        global $wpdb;
        $table_name = $wpdb->prefix . 'administration_example_table';

        $prepared_query = $wpdb->prepare( "SELECT * FROM $table_name WHERE id = %d", $id );
        $result = $wpdb->get_row( $prepared_query );

        if ( null === $result ) {
            error_log( 'No data found for ID: ' . $id );
        }

        return $result;
    }
}
