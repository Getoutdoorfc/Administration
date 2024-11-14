<?php
defined( 'ABSPATH' ) || exit;

use Administration\ProductTypes\Rentals\AjaxHandlers;

// Hent eksisterende tilgængelighedsdatoer
$post_id            = get_the_ID();
$availability_dates = get_post_meta( $post_id, '_rental_availability', true );

?>

<div id="rental_availability_wrapper">
    <table id="rental_availability" class="widefat">
        <thead>
            <tr>
                <th><?php esc_html_e( 'Startdato', 'administration' ); ?></th>
                <th><?php esc_html_e( 'Slutdato', 'administration' ); ?></th>
                <th><?php esc_html_e( 'Handling', 'administration' ); ?></th>
            </tr>
        </thead>
        <tbody>
            <?php
            if ( ! empty( $availability_dates ) && is_array( $availability_dates ) ) {
                foreach ( $availability_dates as $index => $date ) {
                    include 'availability-row.php';
                }
            } else {
                $index = 0;
                $date  = array();
                include 'availability-row.php';
            }
            ?>
        </tbody>
    </table>
    <button type="button" class="button" id="add_rental_availability"><?php esc_html_e( 'Tilføj Tilgængelig Dato', 'administration' ); ?></button>
</div>

<script type="text/javascript">
jQuery(document).ready(function($) {
    $('#add_rental_availability').on('click', function() {
        var index = $('#rental_availability .rental_availability_row').length;
        var data = {
            action: 'add_rental_availability_row',
            index: index,
            security: '<?php echo wp_create_nonce( 'add_rental_availability_row_nonce' ); ?>'
        };
        $.post(ajaxurl, data, function(response) {
            if (response.success) {
                $('#rental_availability tbody').append(response.data);
            }
        });
    });
});
</script>
