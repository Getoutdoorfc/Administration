<?php
namespace Administration\Components\ProductTypes;

use WC_Product;

defined( 'ABSPATH' ) || exit;

/**
 * Class ProductType
 *
 * Baseklasse for alle tilpassede produkttyper.
 *
 * @package Administration\Components\ProductTypes
 */
abstract class ProductType extends WC_Product {

    /**
     * Constructor
     *
     * @param mixed $product
     */
    public function __construct( $product = 0 ) {
        parent::__construct( $product );
    }

    /**
     * Initieringsmetode til at registrere produkttypen.
     */
    abstract public static function init();
}
