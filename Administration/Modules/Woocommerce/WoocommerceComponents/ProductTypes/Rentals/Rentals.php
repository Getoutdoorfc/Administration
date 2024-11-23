<?php
namespace Administration\Components\ProductTypes\Rentals;

use WC_Product;

defined( 'ABSPATH' ) || exit;

/**
 * Class Rentals
 *
 * Definerer produkttypen "Rentals" i WooCommerce.
 *
 * @package Administration\Components\ProductTypes\Rentals
 */
class Rentals extends WC_Product {

    /**
     * Constructor for the Rentals product type.
     *
     * @param mixed $product
     */
    public function __construct( $product = 0 ) {
        $this->set_props( array(
            'product_type' => 'rentals',
        ) );
        parent::__construct( $product );
    }

    /**
     * Registrerer produkttypen og initialiserer tilpassede felter.
     */
    public static function init() {
        add_filter( 'product_type_selector', array( __CLASS__, 'add_rentals_product_type' ) );
        add_action( 'init', array( __CLASS__, 'register_product_type' ) );
    }

    /**
     * Tilføjer 'Rentals' til produkt-typevælgeren.
     *
     * @param array $types
     * @return array
     */
    public static function add_rentals_product_type( $types ) {
        $types['rentals'] = __( 'Udlejning', 'administration' );
        return $types;
    }

    /**
     * Registrerer produkttypen 'rentals' i WooCommerce.
     */
    public static function register_product_type() {
        class_alias( __CLASS__, 'WC_Product_Rentals' );
    }
}

// Initialiserer klassen
Rentals::init();
