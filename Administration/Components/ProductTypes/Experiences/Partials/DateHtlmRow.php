<?php
defined( 'ABSPATH' ) || exit;

?>

<tr class="experience_date_row">
    <td>
        <label><?php esc_html_e( 'Dato og Starttidspunkt (YYYY-MM-DDTHH)', 'administration' ); ?></label>
        <input type="text" name="_experience_dates[<?php echo esc_attr( $index ); ?>][start_time]" value="<?php echo isset( $date['start_time'] ) ? esc_attr( $date['start_time'] ) : ''; ?>" placeholder="2024-10-05T13:00" />
    </td>
    <td>
        <label><?php esc_html_e( 'Varighed (timer)', 'administration' ); ?></label>
        <input type="number" step="0.1" name="_experience_dates[<?php echo esc_attr( $index ); ?>][duration]" value="<?php echo isset( $date['duration'] ) ? esc_attr( $date['duration'] ) : ''; ?>" />
    </td>
    <td>
        <label><?php esc_html_e( 'Variant SKU', 'administration' ); ?></label>
        <input type="text" name="_experience_dates[<?php echo esc_attr( $index ); ?>][sku]" value="<?php echo isset( $date['sku'] ) ? esc_attr( $date['sku'] ) : ''; ?>" />
    </td>
    <td>
        <label><?php esc_html_e( 'Pris', 'administration' ); ?></label>
        <input type="number" step="0.01" name="_experience_dates[<?php echo esc_attr( $index ); ?>][price]" value="<?php echo isset( $date['price'] ) ? esc_attr( $date['price'] ) : ''; ?>" />
    </td>
    <td>
        <label><?php esc_html_e( 'Bemærkninger', 'administration' ); ?></label>
        <textarea name="_experience_dates[<?php echo esc_attr( $index ); ?>][notes]"><?php echo isset( $date['notes'] ) ? esc_textarea( $date['notes'] ) : ''; ?></textarea>
    </td>
    <td>
        <label><?php esc_html_e( 'Lagerantal', 'administration' ); ?></label>
        <input type="number" name="_experience_dates[<?php echo esc_attr( $index ); ?>][stock]" value="<?php echo isset( $date['stock'] ) ? esc_attr( $date['stock'] ) : ''; ?>" />
    </td>
    <td>
        <button type="button" class="button remove_experience_date"><?php esc_html_e( 'Fjern', 'administration' ); ?></button>
    </td>
</tr>

<script type="text/javascript">
jQuery(document).ready(function($) {
    $('.remove_experience_date').off('click').on('click', function() {
        $(this).closest('.experience_date_row').remove();
    });
});
</script>
