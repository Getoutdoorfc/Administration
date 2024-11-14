<?php
namespace Administration\Components\AdminInterface;

defined( 'ABSPATH' ) || exit;

/**
 * Class StockOverview
 *
 * Viser en lageroversigtsside i admin-området.
 *
 * @package Administration\Components\AdminInterface
 */
class StockOverview {

    /**
     * Viser lageroversigtssiden.
     */
    public function display_stock_overview_page() {
        if ( ! current_user_can( 'manage_options' ) ) {
            return;
        }

        // Håndter formularindsendelser for at opdatere lagerantal
        if ( $_SERVER['REQUEST_METHOD'] === 'POST' && isset( $_POST['administration_lager_nonce'] ) ) {
            if ( ! wp_verify_nonce( $_POST['administration_lager_nonce'], 'administration_update_lager' ) ) {
                wp_die( esc_html__( 'Sikkerhedskontrol fejlede. Prøv igen.', 'administration' ) );
            }

            foreach ( $_POST['stock'] as $product_id => $stock_quantity ) {
                $stock_quantity = intval( $stock_quantity );
                update_post_meta( $product_id, '_stock', $stock_quantity );
            }

            echo '<div class="notice notice-success is-dismissible"><p>' . esc_html__( 'Lager opdateret.', 'administration' ) . '</p></div>';
        }

        // Hent lagerdata
        $stock_data = array();
        $args       = array(
            'post_type'      => array( 'product', 'product_variation' ),
            'posts_per_page' => -1,
            'post_status'    => 'publish',
        );
        $products   = get_posts( $args );

        foreach ( $products as $product ) {
            $product_id     = $product->ID;
            $product_name   = get_the_title( $product_id );
            $product_sku    = get_post_meta( $product_id, '_sku', true );
            $stock_quantity = get_post_meta( $product_id, '_stock', true );

            $stock_data[] = array(
                'id'             => $product_id,
                'name'           => $product_name,
                'sku'            => $product_sku,
                'stock_quantity' => $stock_quantity,
            );
        }

        // Inkluderer templaten
        include ADMINISTRATION_PLUGIN_DIR . 'components/admin-interface/templates/stock-overview-page.php';
    }
}
