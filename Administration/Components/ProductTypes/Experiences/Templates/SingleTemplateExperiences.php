<?php
defined( 'ABSPATH' ) || exit;

global $product;

?>

<div class="experience-date-picker">
    <label for="experience_date_picker"><?php esc_html_e( 'Vælg Dato og Tid:', 'administration' ); ?></label>
    <select id="experience_date_picker" name="experience_date">
        <?php
        $dates = get_post_meta( $product->get_id(), '_experience_dates', true );
        if ( ! empty( $dates ) ) {
            foreach ( $dates as $date ) {
                if ( isset( $date['start_time'] ) && ! empty( $date['start_time'] ) ) {
                    echo '<option value="' . esc_attr( $date['start_time'] ) . '">' . esc_html( $date['start_time'] ) . '</option>';
                }
            }
        }
        ?>
    </select>
</div>

<?php include 'tilvalg-fields.php'; ?>

<?php do_action( 'woocommerce_before_add_to_cart_button' ); ?>

<button type="submit" class="single_add_to_cart_button button alt"><?php echo esc_html( $product->single_add_to_cart_text() ); ?></button>

<?php do_action( 'woocommerce_after_add_to_cart_button' ); ?>

