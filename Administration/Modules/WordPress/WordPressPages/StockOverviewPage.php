<?php namespace Administration\Modules\WordPress\WordPressPages;
defined( 'ABSPATH' ) || exit;


/**
 * Lageroversigt Template
 *
 * Viser en oversigt over lagerbeholdningen i admin-grænsefladen.
 *
 * @package Administration\Modules\WordPress\WordPressPages
 */

?>

<div class="wrap">
    <h1><?php esc_html_e( 'Lageroversigt', 'administration' ); ?></h1>
    <form method="post">
        <?php wp_nonce_field( 'administration_update_lager', 'administration_lager_nonce' ); ?>
        <table class="wp-list-table widefat fixed striped">
            <thead>
                <tr>
                    <th><?php esc_html_e( 'Produktnavn', 'administration' ); ?></th>
                    <th><?php esc_html_e( 'SKU', 'administration' ); ?></th>
                    <th><?php esc_html_e( 'Lagerantal', 'administration' ); ?></th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ( $stock_data as $item ) : ?>
                    <tr>
                        <td><?php echo esc_html( $item['name'] ); ?></td>
                        <td><?php echo esc_html( $item['sku'] ); ?></td>
                        <td><input type="number" name="stock[<?php echo esc_attr( $item['id'] ); ?>]" value="<?php echo esc_attr( $item['stock_quantity'] ); ?>" /></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        <?php submit_button( __( 'Opdater Lager', 'administration' ) ); ?>
    </form>
</div>
