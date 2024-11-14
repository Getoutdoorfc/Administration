<?php
namespace Administration\Components\ProductTypes\Experiences;

use WC_Product_Variable;
use Administration\Utilities\Validation;

defined( 'ABSPATH' ) || exit;

/**
 * Class DateOptions
 *
 * Håndterer dato- og tidsindstillinger for oplevelsesprodukttypen.
 *
 * @package Administration\Components\ProductTypes\Experiences
 */
class DateOptions {

    /**
     * Constructor
     */
    public function __construct() {
        add_action( 'woocommerce_product_after_variable_attributes', array( $this, 'add_date_options' ), 10, 3 );
        add_action( 'woocommerce_save_product_variation', array( $this, 'save_date_options' ), 10, 2 );
    }

    /**
     * Tilføjer datoindstillinger til variationer.
     *
     * @param int   $loop
     * @param array $variation_data
     * @param WP_Post $variation
     */
    public function add_date_options( $loop, $variation_data, $variation ) {
        // Felt for Dato og Starttidspunkt
        woocommerce_wp_text_input( array(
            'id'            => '_experience_date_' . $variation->ID,
            'name'          => '_experience_date[' . $variation->ID . ']',
            'label'         => __( 'Dato og Starttidspunkt', 'administration' ),
            'placeholder'   => 'YYYY-MM-DDTHH:MM',
            'desc_tip'      => true,
            'description'   => __( 'Indtast dato og starttidspunkt i formatet YYYY-MM-DDTHH:MM.', 'administration' ),
            'value'         => get_post_meta( $variation->ID, '_experience_date', true ),
            'wrapper_class' => 'form-row form-row-full',
        ) );

        // Varighed
        woocommerce_wp_text_input( array(
            'id'            => '_experience_duration_' . $variation->ID,
            'name'          => '_experience_duration[' . $variation->ID . ']',
            'label'         => __( 'Varighed (timer)', 'administration' ),
            'type'          => 'number',
            'desc_tip'      => true,
            'description'   => __( 'Varighed i timer.', 'administration' ),
            'value'         => get_post_meta( $variation->ID, '_experience_duration', true ),
            'wrapper_class' => 'form-row form-row-full',
        ) );

        // Dato/Variant SKU
        woocommerce_wp_text_input( array(
            'id'            => '_experience_sku_' . $variation->ID,
            'name'          => '_experience_sku[' . $variation->ID . ']',
            'label'         => __( 'Dato/Variant SKU', 'administration' ),
            'desc_tip'      => true,
            'description'   => __( 'Unikt SKU for hver dato/variant.', 'administration' ),
            'value'         => get_post_meta( $variation->ID, '_experience_sku', true ),
            'wrapper_class' => 'form-row form-row-full',
        ) );

        // Pris
        woocommerce_wp_text_input( array(
            'id'            => '_experience_price_' . $variation->ID,
            'name'          => '_experience_price[' . $variation->ID . ']',
            'label'         => __( 'Pris', 'administration' ),
            'type'          => 'number',
            'desc_tip'      => true,
            'description'   => __( 'Pris for denne dato/variant.', 'administration' ),
            'value'         => get_post_meta( $variation->ID, '_experience_price', true ),
            'wrapper_class' => 'form-row form-row-full',
        ) );

        // Bemærkninger
        woocommerce_wp_textarea_input( array(
            'id'            => '_experience_notes_' . $variation->ID,
            'name'          => '_experience_notes[' . $variation->ID . ']',
            'label'         => __( 'Bemærkninger', 'administration' ),
            'desc_tip'      => true,
            'description'   => __( 'Ekstra oplysninger om denne dato/variant.', 'administration' ),
            'value'         => get_post_meta( $variation->ID, '_experience_notes', true ),
            'wrapper_class' => 'form-row form-row-full',
        ) );

        // Lagerstyring
        woocommerce_wp_checkbox( array(
            'id'            => '_experience_manage_stock_' . $variation->ID,
            'name'          => '_experience_manage_stock[' . $variation->ID . ']',
            'label'         => __( 'Lagerstyring', 'administration' ),
            'description'   => __( 'Aktiver lagerstyring for denne dato/variant.', 'administration' ),
            'value'         => get_post_meta( $variation->ID, '_experience_manage_stock', true ),
        ) );

        woocommerce_wp_text_input( array(
            'id'            => '_experience_stock_quantity_' . $variation->ID,
            'name'          => '_experience_stock_quantity[' . $variation->ID . ']',
            'label'         => __( 'Antal på lager', 'administration' ),
            'type'          => 'number',
            'desc_tip'      => true,
            'description'   => __( 'Antal pladser tilgængelige for denne dato/variant.', 'administration' ),
            'value'         => get_post_meta( $variation->ID, '_experience_stock_quantity', true ),
            'wrapper_class' => 'form-row form-row-full',
        ) );
    }

    /**
     * Gemmer datoindstillinger for variationer.
     *
     * @param int $variation_id
     * @param int $i
     */
    public function save_date_options( $variation_id, $i ) {
        $validation = new Validation();

        // Dato og Starttidspunkt
        $date = isset( $_POST['_experience_date'][ $variation_id ] ) ? sanitize_text_field( $_POST['_experience_date'][ $variation_id ] ) : '';
        if ( ! $validation->validate_date( $date ) ) {
            // Håndter ugyldig dato (fx tilføj en admin notice)
            return;
        }
        update_post_meta( $variation_id, '_experience_date', $date );

        // Varighed
        $duration = isset( $_POST['_experience_duration'][ $variation_id ] ) ? sanitize_text_field( $_POST['_experience_duration'][ $variation_id ] ) : '';
        update_post_meta( $variation_id, '_experience_duration', $duration );

        // SKU
        $sku = isset( $_POST['_experience_sku'][ $variation_id ] ) ? sanitize_text_field( $_POST['_experience_sku'][ $variation_id ] ) : '';
        update_post_meta( $variation_id, '_experience_sku', $sku );

        // Pris
        $price = isset( $_POST['_experience_price'][ $variation_id ] ) ? sanitize_text_field( $_POST['_experience_price'][ $variation_id ] ) : '';
        update_post_meta( $variation_id, '_experience_price', $price );

        // Bemærkninger
        $notes = isset( $_POST['_experience_notes'][ $variation_id ] ) ? sanitize_textarea_field( $_POST['_experience_notes'][ $variation_id ] ) : '';
        update_post_meta( $variation_id, '_experience_notes', $notes );

        // Lagerstyring
        $manage_stock = isset( $_POST['_experience_manage_stock'][ $variation_id ] ) ? 'yes' : 'no';
        update_post_meta( $variation_id, '_manage_stock', $manage_stock );

        // Antal på lager
        $stock_quantity = isset( $_POST['_experience_stock_quantity'][ $variation_id ] ) ? intval( $_POST['_experience_stock_quantity'][ $variation_id ] ) : '';
        update_post_meta( $variation_id, '_stock', $stock_quantity );
    }
}

// Initialiserer klassen
new DateOptions();
