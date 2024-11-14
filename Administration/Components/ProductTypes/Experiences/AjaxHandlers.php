<?php
namespace Administration\Components\ProductTypes\Experiences;

use Administration\Utilities\Validation;

defined( 'ABSPATH' ) || exit;

/**
 * Class AjaxHandlers
 *
 * Håndterer AJAX-anmodninger for oplevelsesprodukttypen.
 *
 * @package Administration\Components\ProductTypes\Experiences
 */
class AjaxHandlers {

    /**
     * Constructor
     */
    public function __construct() {
        add_action( 'wp_ajax_add_experience_date_row', array( $this, 'add_experience_date_row' ) );
        add_action( 'wp_ajax_add_experience_tilvalg_row', array( $this, 'add_experience_tilvalg_row' ) );
    }

    /**
     * AJAX callback for at tilføje en dato række.
     */
    public function add_experience_date_row() {
        check_ajax_referer( 'add_experience_date_row_nonce', 'security' );

        $index = intval( $_POST['index'] );
        $date  = array();

        ob_start();
        include 'partials/date-row.php';
        $output = ob_get_clean();

        wp_send_json_success( $output );
    }

    /**
     * AJAX callback for at tilføje en tilvalg række.
     */
    public function add_experience_tilvalg_row() {
        check_ajax_referer( 'add_experience_tilvalg_row_nonce', 'security' );

        $index = intval( $_POST['index'] );
        $item  = array();

        ob_start();
        include 'partials/tilvalg-row.php';
        $output = ob_get_clean();

        wp_send_json_success( $output );
    }
}

// Initialiserer klassen
new AjaxHandlers();
