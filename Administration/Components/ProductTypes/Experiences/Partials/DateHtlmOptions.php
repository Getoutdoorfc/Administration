<?php
defined( 'ABSPATH' ) || exit;

use Administration\ProductTypes\Experiences\AjaxHandlers;

// Henter eksisterende datoer
$post_id = get_the_ID();
$dates   = get_post_meta( $post_id, '_experience_dates', true );

?>

<div id="experience_dates_wrapper">
    <div id="experience_dates">
        <?php
        if ( ! empty( $dates ) && is_array( $dates ) ) {
            foreach ( $dates as $index => $date ) {
                include 'date-row.php';
            }
        } else {
            $index = 0;
            $date  = array();
            include 'date-row.php';
        }
        ?>
    </div>
    <button type="button" class="button" id="add_experience_date"><?php esc_html_e( 'Tilføj Dato', 'administration' ); ?></button>
</div>

<script type="text/javascript">
jQuery(document).ready(function($) {
    $('#add_experience_date').on('click', function() {
        var index = $('#experience_dates .experience_date_row').length;
        var data = {
            action: 'add_experience_date_row',
            index: index,
            security: '<?php echo wp_create_nonce( 'add_experience_date_row_nonce' ); ?>'
        };
        $.post(ajaxurl, data, function(response) {
            if (response.success) {
                $('#experience_dates').append(response.data);
            }
        });
    });
});
</script>
