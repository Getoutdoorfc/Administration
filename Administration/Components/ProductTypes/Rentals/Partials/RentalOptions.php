<?php
defined( 'ABSPATH' ) || exit;

// Hent eksisterende udlejningsindstillinger
$post_id         = get_the_ID();
$rental_options  = get_post_meta( $post_id, '_rental_options', true );

?>

<div id="rental_options_wrapper" class="options_group">
    <?php
    // Pris pr. dag
    woocommerce_wp_text_input( array(
        'id'          => '_rental_options_price_per_day',
        'name'        => '_rental_options[price_per_day]',
        'label'       => __( 'Pris pr. dag', 'administration' ),
        'type'        => 'number',
        'step'        => '0.01',
        'description' => __( 'Angiv pris pr. dag for udlejning.', 'administration' ),
        'desc_tip'    => true,
        'value'       => isset( $rental_options['price_per_day'] ) ? esc_attr( $rental_options['price_per_day'] ) : '',
    ) );

    // Udlejningsbetingelser
    woocommerce_wp_textarea_input( array(
        'id'          => '_rental_options_terms',
        'name'        => '_rental_options[terms]',
        'label'       => __( 'Udlejningsbetingelser', 'administration' ),
        'description' => __( 'Angiv udlejningsbetingelserne.', 'administration' ),
        'desc_tip'    => true,
        'value'       => isset( $rental_options['terms'] ) ? esc_textarea( $rental_options['terms'] ) : '',
    ) );

    // Lokationer
    woocommerce_wp_text_input( array(
        'id'          => '_rental_options_locations',
        'name'        => '_rental_options[locations]',
        'label'       => __( 'Lokationer (kommasepareret)', 'administration' ),
        'description' => __( 'Angiv tilgængelige lokationer for udlejning.', 'administration' ),
        'desc_tip'    => true,
        'value'       => isset( $rental_options['locations'] ) ? esc_attr( $rental_options['locations'] ) : '',
    ) );
    ?>
</div>
