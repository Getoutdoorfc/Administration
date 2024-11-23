<?php
namespace Administration\Components\ProductTypes\Experiences;

defined( 'ABSPATH' ) || exit;

/**
 * Class RemoveFields
 *
 * Fjerner irrelevante felter for oplevelsesprodukttypen.
 *
 * @package Administration\Components\ProductTypes\Experiences
 */
class RemoveFields {

    /**
     * Constructor
     */
    public function __construct() {
        add_filter( 'woocommerce_product_data_tabs', array( $this, 'remove_product_data_tabs' ), 20 );
        add_action( 'admin_head', array( $this, 'hide_unnecessary_fields' ) );
    }

    /**
     * Fjerner irrelevante produktdata tabs.
     *
     * @param array $tabs
     * @return array
     */
    public function remove_product_data_tabs( $tabs ) {
        global $product_object;

        if ( 'experience' === $product_object->get_type() ) {
            unset( $tabs['shipping'] );
            unset( $tabs['advanced'] );
            unset( $tabs['attributes'] );
        }

        return $tabs;
    }

    /**
     * Skjuler unødvendige felter via CSS.
     */
    public function hide_unnecessary_fields() {
        global $post, $product_object;

        if ( 'experience' === $product_object->get_type() ) {
            echo '<style>
                .product_data_tabs .shipping_tab,
                .product_data_tabs .advanced_tab,
                .product_data_tabs .attribute_tab,
                #general_product_data ._sku_field,
                #inventory_product_data ._manage_stock_field,
                #inventory_product_data ._sold_individually_field {
                    display: none !important;
                }
            </style>';
        }
    }
}

// Initialiserer klassen
new RemoveFields();
