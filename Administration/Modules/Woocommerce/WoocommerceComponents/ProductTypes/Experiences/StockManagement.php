<?php
namespace Administration\Components\ProductTypes\Experiences;

use WC_Order;
use WC_Product;

defined( 'ABSPATH' ) || exit;

/**
 * Class StockManagement
 *
 * Håndterer lagerstyring for oplevelsesprodukttypen.
 *
 * @package Administration\Components\ProductTypes\Experiences
 */
class StockManagement {

    /**
     * Constructor
     */
    public function __construct() {
        add_action( 'woocommerce_reduce_order_stock', array( $this, 'reduce_experience_stock' ) );
    }

    /**
     * Reducerer lager for experience datoer.
     *
     * @param WC_Order $order
     */
    public function reduce_experience_stock( $order ) {
        foreach ( $order->get_items() as $item_id => $item ) {
            $product = $item->get_product();

            if ( $product && 'experience' === $product->get_type() ) {
                $experience_date = $item->get_meta( '_experience_date' );

                // Hent nuværende lager
                $dates = get_post_meta( $product->get_id(), '_experience_dates', true );

                if ( ! empty( $dates ) ) {
                    foreach ( $dates as &$date ) {
                        if ( isset( $date['start_time'] ) && $date['start_time'] === $experience_date ) {
                            $date['stock'] = intval( $date['stock'] ) - $item->get_quantity();
                            // Forhindre negativ lager
                            if ( $date['stock'] < 0 ) {
                                $date['stock'] = 0;
                            }
                            break;
                        }
                    }

                    // Opdaterer datoerne med den nye lagerstatus
                    update_post_meta( $product->get_id(), '_experience_dates', $dates );
                }
            }
        }
    }
}

// Initialiserer klassen
new StockManagement();
