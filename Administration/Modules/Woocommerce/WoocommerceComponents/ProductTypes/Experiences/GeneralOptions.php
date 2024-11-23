<?php
namespace Administration\Components\ProductTypes\Experiences;

use Administration\Components\Utilities\Helpers;

defined( 'ABSPATH' ) || exit;

/**
 * Class GeneralOptions
 *
 * Håndterer generelle indstillinger for oplevelsesprodukttypen.
 *
 * @package Administration\Components\ProductTypes\Experiences
 */
class GeneralOptions {

    /**
     * Constructor
     */
    public function __construct() {
        add_action( 'woocommerce_product_options_general_product_data', array( $this, 'add_general_options' ) );
        add_action( 'woocommerce_process_product_meta_experience', array( $this, 'save_general_options' ) );
    }

    /**
     * Tilføjer generelle indstillinger til produkttypen.
     */
    public function add_general_options() {
        global $post;

        echo '<div class="options_group show_if_experience">';

        // Kategorifarve
        $categories = ( new Helpers() )->get_calendar_categories();
        $options    = array( '' => __( 'Vælg en kategorifarve', 'administration' ) );
        if ( ! empty( $categories ) ) {
            foreach ( $categories as $category ) {
                $options[ $category['displayName'] ] = $category['displayName'];
            }
        }

        woocommerce_wp_select( array(
            'id'          => '_experience_category_color',
            'label'       => __( 'Kategorifarve', 'administration' ),
            'options'     => $options,
            'description' => __( 'Synkroniser den valgte kategorifarve med Microsoft-kategorier.', 'administration' ),
            'desc_tip'    => true,
        ) );

        echo '</div>';
    }

    /**
     * Gemmer generelle indstillinger for produkttypen.
     *
     * @param int $post_id
     */
    public function save_general_options( $post_id ) {
        $category_color = isset( $_POST['_experience_category_color'] ) ? sanitize_text_field( $_POST['_experience_category_color'] ) : '';
        update_post_meta( $post_id, '_experience_category_color', $category_color );
    }
}

// Initialiserer klassen
new GeneralOptions();
