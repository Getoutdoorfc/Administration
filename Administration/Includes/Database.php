<?php
namespace Administration\Includes;

defined( 'ABSPATH' ) || exit;

/**
 * Class Database
 *
 * Håndterer databaseinteraktioner for pluginet.
 *
 * @package Administration\Includes
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

        $sanitized_data = array_map( 'sanitize_text_field', $data );

        $wpdb->insert( $table_name, $sanitized_data );
    }

    /**
     * Henter data fra databasen.
     *
     * @param int $id ID for posten.
     * @return object|null Dataobjekt eller null hvis ikke fundet.
     */
    public static function getData( $id ) {
        global $wpdb;
        $table_name = $wpdb->prefix . 'administration_example_table';

        $prepared_query = $wpdb->prepare( "SELECT * FROM $table_name WHERE id = %d", $id );
        return $wpdb->get_row( $prepared_query );
    }
}
