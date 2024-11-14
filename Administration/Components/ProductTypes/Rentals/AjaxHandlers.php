<?php
namespace Administration\Components\ProductTypes\Rentals;

defined( 'ABSPATH' ) || exit;

/**
 * Class AjaxHandlers
 *
 * Håndterer AJAX-anmodninger for produkttypen "Rentals".
 *
 * @package Administration\Components\ProductTypes\Rentals
 */
class AjaxHandlers {

    /**
     * Constructor
     */
    public function __construct() {
        add_action( 'wp_ajax_add_rental_availability_row', array( $this, 'add_rental_availability_row' ) );
    }

    /**
     * AJAX callback for at tilføje en tilgængeligheds række.
     */
    public function add_rental_availability_row() {
        check_ajax_referer( 'add_rental_availability_row_nonce', 'security' );

        $index = intval( $_POST['index'] );
        $date  = array();

        ob_start();
        include 'partials/availability-row.php';
        $output = ob_get_clean();

        wp_send_json_success( $output );
    }
}

// Initialiserer klassen
new AjaxHandlers();
