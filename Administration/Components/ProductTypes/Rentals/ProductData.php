<?php
namespace Administration\Components\ProductTypes\Rentals;

use Administration\Components\Utilities\Validation;

defined( 'ABSPATH' ) || exit;

/**
 * Class ProductData
 *
 * Håndterer produktdata for produkttypen "Rentals".
 *
 * @package Administration\Components\ProductTypes\Rentals
 */
class ProductData {

    /**
     * Constructor
     */
    public function __construct() {
        add_filter( 'woocommerce_product_data_tabs', array( $this, 'add_product_data_tabs' ) );
        add_action( 'woocommerce_product_data_panels', array( $this, 'add_product_data_panels' ) );
        add_action( 'woocommerce_process_product_meta_rentals', array( $this, 'save_product_data' ) );
    }

    /**
     * Tilføjer tilpassede produktdatatabs.
     *
     * @param array $tabs
     * @return array
     */
    public function add_product_data_tabs( $tabs ) {
        $tabs['availability_options'] = array(
            'label'    => __( 'Tilgængelighed', 'administration' ),
            'target'   => 'availability_options',
            'class'    => array( 'show_if_rentals' ),
            'priority' => 21,
        );

        $tabs['rental_options'] = array(
            'label'    => __( 'Udlejningsindstillinger', 'administration' ),
            'target'   => 'rental_options',
            'class'    => array( 'show_if_rentals' ),
            'priority' => 22,
        );

        return $tabs;
    }

    /**
     * Tilføjer tilpassede produktdatapaneler.
     */
    public function add_product_data_panels() {
        global $post;

        // Tilgængelighed panel
        echo '<div id="availability_options" class="panel woocommerce_options_panel">';
        include 'partials/availability-options.php';
        echo '</div>';

        // Udlejningsindstillinger panel
        echo '<div id="rental_options" class="panel woocommerce_options_panel">';
        include 'partials/rental-options.php';
        echo '</div>';
    }

    /**
     * Gemmer tilpassede produktdata.
     *
     * @param int $post_id
     */
    public function save_product_data( $post_id ) {
        $validation = new Validation();

        // Gemmer availability options
        if ( isset( $_POST['_rental_availability'] ) ) {
            $availability = array();

            foreach ( $_POST['_rental_availability'] as $index => $date ) {
                $start_date = sanitize_text_field( $date['start_date'] );
                $end_date   = sanitize_text_field( $date['end_date'] );

                // Validerer datoformat
                if ( ! $validation->validate_date( $start_date, 'Y-m-d' ) || ! $validation->validate_date( $end_date, 'Y-m-d' ) ) {
                    continue; // Spring over ugyldige datoer
                }

                $availability[] = array(
                    'start_date' => $start_date,
                    'end_date'   => $end_date,
                );
            }

            update_post_meta( $post_id, '_rental_availability', $availability );
        }

        // Gemmer rental options
        if ( isset( $_POST['_rental_options'] ) ) {
            $rental_options = array(
                'price_per_day' => floatval( $_POST['_rental_options']['price_per_day'] ),
                'terms'         => sanitize_textarea_field( $_POST['_rental_options']['terms'] ),
                'locations'     => sanitize_text_field( $_POST['_rental_options']['locations'] ),
            );

            update_post_meta( $post_id, '_rental_options', $rental_options );
        }
    }
}

// Initialiserer klassen
new ProductData();
