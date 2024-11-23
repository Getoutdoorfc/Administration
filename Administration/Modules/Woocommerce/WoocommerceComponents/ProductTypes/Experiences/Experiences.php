<?php
namespace Administration\Components\ProductTypes\Experiences;

use WC_Product;

defined( 'ABSPATH' ) || exit;

/**
 * Class Experiences
 *
 * Definerer produkttypen "Experiences" i WooCommerce.
 *
 * @package Administration\Components\ProductTypes\Experiences
 */
class Experiences extends WC_Product {

    /**
     * Constructor
     *
     * @param int $product
     */
    public function __construct( $product = 0 ) {
        $this->product_type = 'experience';
        parent::__construct( $product );
    }

    /**
     * Registrerer produkttypen og initialiserer tilpassede felter.
     */
    public static function init() {
        add_filter( 'product_type_selector', array( __CLASS__, 'add_experience_product_type' ) );
        add_action( 'init', array( __CLASS__, 'register_product_type' ) );
    }

    /**
     * Tilføjer 'Experience' til produkt-typevælgeren.
     *
     * @param array $types
     * @return array
     */
    public static function add_experience_product_type( $types ) {
        $types['experience'] = __( 'Oplevelse', 'administration' );
        return $types;
    }

    /**
     * Registrerer produkttypen 'experience' i WooCommerce.
     */
    public static function register_product_type() {
        class_alias( __CLASS__, 'WC_Product_Experience' );
    }
}

// Initialiserer klassen
Experiences::init();
