<?php
defined( 'ABSPATH' ) || exit;

global $product;

// Enqueue Flatpickr CSS and JS
wp_enqueue_style( 'flatpickr-css', 'https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css' );
wp_enqueue_script( 'flatpickr-js', 'https://cdn.jsdelivr.net/npm/flatpickr', array(), null, true );

// Enqueue frontend.js
wp_enqueue_script( 'administration-frontend-js', plugin_dir_url( __FILE__ ) . '../../assets/js/frontend.js', array( 'jquery', 'flatpickr-js' ), '1.0', true );

// Hent tilgængelighedsdatoer
$availability       = get_post_meta( $product->get_id(), '_rental_availability', true );
$availability_dates = array();

if ( ! empty( $availability ) ) {
    foreach ( $availability as $date ) {
        $availability_dates[] = array(
            'from' => $date['start_date'],
            'to'   => $date['end_date'],
        );
    }
}

// Lokaliser script med tilgængelighedsdata
wp_localize_script( 'administration-frontend-js', 'rental_product_params', array(
    'availability' => $availability_dates,
) );

?>

<?php if ( $product && 'rentals' === $product->get_type() ) : ?>
    <div class="rental-date-picker">
        <label for="rental_start_date"><?php esc_html_e( 'Vælg Startdato:', 'administration' ); ?></label>
        <input type="text" id="rental_start_date" name="rental_start_date" />
        <label for="rental_end_date"><?php esc_html_e( 'Vælg Slutdato:', 'administration' ); ?></label>
        <input type="text" id="rental_end_date" name="rental_end_date" />
    </div>
<?php endif; ?>
