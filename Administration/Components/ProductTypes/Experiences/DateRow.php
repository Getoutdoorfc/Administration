<tr class="experience_date_row">
    <td>
        <label><?php _e( 'Dato og Starttidspunkt (YYYY-MM-DDTHH)', 'administration' ); ?></label>
        <input type="text" name="_experience_dates[<?php echo $index; ?>][start_time]" value="<?php echo isset( $date['start_time'] ) ? esc_attr( $date['start_time'] ) : ''; ?>" placeholder="2024-10-05T13:00" />
    </td>
    <td>
        <label><?php _e( 'Varighed (timer)', 'administration' ); ?></label>
        <input type="number" step="0.1" name="_experience_dates[<?php echo $index; ?>][duration]" value="<?php echo isset( $date['duration'] ) ? esc_attr( $date['duration'] ) : ''; ?>" />
    </td>
    <td>
        <label><?php _e( 'Variant SKU', 'administration' ); ?></label>
        <input type="text" name="_experience_dates[<?php echo $index; ?>][sku]" value="<?php echo isset( $date['sku'] ) ? esc_attr( $date['sku'] ) : ''; ?>" />
    </td>
    <td>
        <label><?php _e( 'Pris', 'administration' ); ?></label>
        <input type="text" name="_experience_dates[<?php echo $index; ?>][price]" value="<?php echo isset( $date['price'] ) ? esc_attr( $date['price'] ) : ''; ?>" />
    </td>
    <td>
        <label><?php _e( 'Bemærkninger', 'administration' ); ?></label>
        <textarea name="_experience_dates[<?php echo $index; ?>][notes]"><?php echo isset( $date['notes'] ) ? esc_textarea( $date['notes'] ) : ''; ?></textarea>
    </td>
    <td>
        <label><?php _e( 'Lagerantal', 'administration' ); ?></label>
        <input type="number" name="_experience_dates[<?php echo $index; ?>][stock]" value="<?php echo isset( $date['stock'] ) ? esc_attr( $date['stock'] ) : ''; ?>" />
    </td>
    <td>
        <button type="button" class="button remove_experience_date"><?php _e( 'Fjern', 'administration' ); ?></button>
    </td>
</tr>

<script type="text/javascript">
jQuery(document).ready(function($) {
    $('.remove_experience_date').on('click', function() {
        $(this).closest('.experience_date_row').remove();
    });
});
</script>
