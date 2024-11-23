<?php
namespace Administration\Components\ProductTypes\Experiences;

use WC_Product;

defined( 'ABSPATH' ) || exit;

/**
 * Class TilvalgOptions
 *
 * Håndterer tilvalgsindstillinger for oplevelsesprodukttypen.
 *
 * @package Administration\Components\ProductTypes\Experiences
 */
class TilvalgOptions {

    /**
     * Constructor
     */
    public function __construct() {
        add_action( 'woocommerce_product_data_panels', array( $this, 'add_tilvalg_options_panel' ) );
        add_action( 'woocommerce_process_product_meta_experience', array( $this, 'save_tilvalg_options' ) );
    }

    /**
     * Tilføjer tilvalgsindstillinger til produktet.
     */
    public function add_tilvalg_options_panel() {
        global $post;

        echo '<div id="tilvalg_options" class="panel woocommerce_options_panel">';
        include 'partials/tilvalg-options.php';
        echo '</div>';
    }

    /**
     * Gemmer tilvalgsindstillinger for produktet.
     *
     * @param int $post_id
     */
    public function save_tilvalg_options( $post_id ) {
        if ( isset( $_POST['_experience_tilvalg'] ) ) {
            $tilvalg_clean = array();

            foreach ( $_POST['_experience_tilvalg'] as $index => $item ) {
                $name    = sanitize_text_field( $item['name'] );
                $options = sanitize_text_field( $item['options'] );

                if ( ! empty( $name ) && ! empty( $options ) ) {
                    $tilvalg_clean[] = array(
                        'name'    => $name,
                        'options' => $options,
                    );
                }
            }

            update_post_meta( $post_id, '_experience_tilvalg', $tilvalg_clean );
        }
    }
}

// Initialiserer klassen
new TilvalgOptions();
