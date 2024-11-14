<?php
defined( 'ABSPATH' ) || exit;
?>

<tr class="rental_availability_row">
    <td>
        <input type="date" name="_rental_availability[<?php echo esc_attr( $index ); ?>][start_date]" value="<?php echo isset( $date['start_date'] ) ? esc_attr( $date['start_date'] ) : ''; ?>" placeholder="YYYY-MM-DD" />
    </td>
    <td>
        <input type="date" name="_rental_availability[<?php echo esc_attr( $index ); ?>][end_date]" value="<?php echo isset( $date['end_date'] ) ? esc_attr( $date['end_date'] ) : ''; ?>" placeholder="YYYY-MM-DD" />
    </td>
    <td>
        <button type="button" class="button remove_rental_availability"><?php esc_html_e( 'Fjern', 'administration' ); ?></button>
    </td>
</tr>

<script type="text/javascript">
jQuery(document).ready(function($) {
    $('.remove_rental_availability').off('click').on('click', function() {
        $(this).closest('.rental_availability_row').remove();
    });
});
</script>
