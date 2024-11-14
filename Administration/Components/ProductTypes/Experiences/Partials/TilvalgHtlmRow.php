<?php
defined( 'ABSPATH' ) || exit;

?>

<tr class="experience_tilvalg_row">
    <td>
        <label><?php esc_html_e( 'Tilvalg Navn', 'administration' ); ?></label>
        <input type="text" name="_experience_tilvalg[<?php echo esc_attr( $index ); ?>][name]" value="<?php echo isset( $item['name'] ) ? esc_attr( $item['name'] ) : ''; ?>" />
    </td>
    <td>
        <label><?php esc_html_e( 'Muligheder (kommasepareret)', 'administration' ); ?></label>
        <input type="text" name="_experience_tilvalg[<?php echo esc_attr( $index ); ?>][options]" value="<?php echo isset( $item['options'] ) ? esc_attr( $item['options'] ) : ''; ?>" />
    </td>
    <td>
        <button type="button" class="button remove_experience_tilvalg"><?php esc_html_e( 'Fjern', 'administration' ); ?></button>
    </td>
</tr>

<script type="text/javascript">
jQuery(document).ready(function($) {
    $('.remove_experience_tilvalg').off('click').on('click', function() {
        $(this).closest('.experience_tilvalg_row').remove();
    });
});
</script>
