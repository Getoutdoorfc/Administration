<?php
namespace Administration\Components\ProductTypes\Experiences;

use Administration\Components\Utilities\Validation;
use Administration\Components\Utilities\Helpers;

defined( 'ABSPATH' ) || exit;

/**
 * Class ProductData
 *
 * Håndterer tilføjelse og lagring af tilpassede produktdata for produkttypen "Experience".
 *
 * @package Administration\Components\ProductTypes\Experiences
 */
class ProductData {

    /**
     * Constructor
     */
    public function __construct() {
        add_filter( 'woocommerce_product_data_tabs', array( $this, 'add_product_data_tabs' ) );
        add_action( 'woocommerce_product_data_panels', array( $this, 'add_product_data_panels' ) );
        add_action( 'woocommerce_process_product_meta_experience', array( $this, 'save_product_data' ) );
    }

    /**
     * Tilføjer tilpassede produktdatatabs.
     *
     * @param array $tabs
     * @return array
     */
    public function add_product_data_tabs( $tabs ) {
        $tabs['date_options'] = array(
            'label'    => __( 'Dato og Tid', 'administration' ),
            'target'   => 'date_options',
            'class'    => array( 'show_if_experience' ),
            'priority' => 21,
        );

        $tabs['tilvalg_options'] = array(
            'label'    => __( 'Tilvalg', 'administration' ),
            'target'   => 'tilvalg_options',
            'class'    => array( 'show_if_experience' ),
            'priority' => 22,
        );

        return $tabs;
    }

    /**
     * Tilføjer tilpassede produktdatapaneler.
     */
    public function add_product_data_panels() {
        global $post;

        // Dato og Tid panel
        echo '<div id="date_options" class="panel woocommerce_options_panel">';
        include 'partials/date-options.php';
        echo '</div>';

        // Tilvalg panel
        echo '<div id="tilvalg_options" class="panel woocommerce_options_panel">';
        include 'partials/tilvalg-options.php';
        echo '</div>';
    }

    /**
     * Gemmer tilpassede produktdata.
     *
     * @param int $post_id
     */
    public function save_product_data( $post_id ) {
        // Inkluder valideringsklasse
        $validation = new Validation();

        // Gemmer date options
        if ( isset( $_POST['_experience_dates'] ) ) {
            $dates = array();

            foreach ( $_POST['_experience_dates'] as $index => $date ) {
                $start_time = sanitize_text_field( $date['start_time'] );
                $duration   = sanitize_text_field( $date['duration'] );

                // Validerer datoformat
                if ( ! $validation->validate_date( $start_time ) ) {
                    continue; // Spring over ugyldige datoer
                }

                // Validerer numerisk varighed
                if ( ! $validation->validate_numeric( $duration ) ) {
                    continue;
                }

                // Saniterer andre felter
                $dates[] = array(
                    'start_time' => $start_time,
                    'duration'   => $duration,
                    'sku'        => sanitize_text_field( $date['sku'] ),
                    'price'      => sanitize_text_field( $date['price'] ),
                    'notes'      => sanitize_textarea_field( $date['notes'] ),
                    'stock'      => intval( $date['stock'] ),
                );
            }

            update_post_meta( $post_id, '_experience_dates', $dates );
        }

        // Gemmer tilvalg options
        if ( isset( $_POST['_experience_tilvalg'] ) ) {
            $tilvalg = array();

            foreach ( $_POST['_experience_tilvalg'] as $index => $item ) {
                $name    = sanitize_text_field( $item['name'] );
                $options = sanitize_text_field( $item['options'] );

                if ( empty( $name ) || empty( $options ) ) {
                    continue;
                }

                $tilvalg[] = array(
                    'name'    => $name,
                    'options' => $options,
                );
            }

            update_post_meta( $post_id, '_experience_tilvalg', $tilvalg );
        }
    }
}

// Initialiserer klassen
new ProductData();
