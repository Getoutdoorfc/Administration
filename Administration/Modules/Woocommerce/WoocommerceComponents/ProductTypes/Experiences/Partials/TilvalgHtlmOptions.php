<?php
defined( 'ABSPATH' ) || exit;

use Administration\Components\Utilities\AjaxHandlers;

// Hent eksisterende tilvalg
$post_id = get_the_ID();
$tilvalg = get_post_meta( $post_id, '_experience_tilvalg', true );

?>

<div id="experience_tilvalg_wrapper">
    <table id="experience_tilvalg" class="widefat">
        <thead>
            <tr>
                <th><?php esc_html_e( 'Tilvalg Navn', 'administration' ); ?></th>
                <th><?php esc_html_e( 'Muligheder (kommasepareret)', 'administration' ); ?></th>
                <th><?php esc_html_e( 'Handling', 'administration' ); ?></th>
            </tr>
        </thead>
        <tbody>
            <?php
            if ( ! empty( $tilvalg ) && is_array( $tilvalg ) ) {
                foreach ( $tilvalg as $index => $item ) {
                    include 'tilvalg-row.php';
                }
            } else {
                $index = 0;
                $item  = array();
                include 'tilvalg-row.php';
            }
            ?>
        </tbody>
    </table>
    <button type="button" class="button" id="add_experience_tilvalg"><?php esc_html_e( 'Tilføj Tilvalg', 'administration' ); ?></button>
</div>

<script type="text/javascript">
jQuery(document).ready(function($) {
    $('#add_experience_tilvalg').on('click', function() {
        var index = $('#experience_tilvalg .experience_tilvalg_row').length;
        var data = {
            action: 'add_experience_tilvalg_row',
            index: index,
            security: '<?php echo wp_create_nonce( 'add_experience_tilvalg_row_nonce' ); ?>'
        };
        $.post(ajaxurl, data, function(response) {
            if (response.success) {
                $('#experience_tilvalg tbody').append(response.data);
            }
        });
    });
});
</script>
