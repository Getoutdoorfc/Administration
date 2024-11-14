<?php
namespace Administration\Components\Integrations\Woocommerce;

use WC_Order;

defined( 'ABSPATH' ) || exit;

/**
 * Class EmailCustomization
 *
 * Tilpasser WooCommerce e-mails ved at tilføje en brugerdefineret ordrebekræftelse.
 *
 * @package Administration\Components\Integrations\Woocommerce;
 */
class EmailCustomization {

    /**
     * Constructor.
     */
    public function __construct() {
        add_filter( 'woocommerce_email_classes', array( $this, 'add_custom_email_class' ) );
        add_filter( 'woocommerce_email_actions', array( $this, 'add_custom_email_action' ) );
        add_action( 'woocommerce_order_status_processing', array( $this, 'trigger_custom_email' ), 10, 2 );
    }

    /**
     * Tilføjer tilpasset e-mail-klasse.
     *
     * @param array $email_classes
     * @return array
     */
    public function add_custom_email_class( $email_classes ) {
        $email_classes['Administration_Order_Confirmation_Email'] = new OrderConfirmationEmail();
        return $email_classes;
    }

    /**
     * Tilføjer tilpasset e-mail-handling.
     *
     * @param array $email_actions
     * @return array
     */
    public function add_custom_email_action( $email_actions ) {
        $email_actions[] = 'administration_order_confirmation_notification';
        return $email_actions;
    }

    /**
     * Udløser tilpasset e-mail.
     *
     * @param int      $order_id
     * @param WC_Order $order
     */
    public function trigger_custom_email( $order_id, $order ) {
        if ( ! $order ) {
            $order = wc_get_order( $order_id );
        }
        do_action( 'administration_order_confirmation_notification', $order_id, $order );
    }
}

// Initialiserer klassen
new EmailCustomization();
