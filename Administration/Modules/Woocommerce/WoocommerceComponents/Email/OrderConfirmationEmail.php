<?php
namespace Administration\Components\Integrations\Woocommerce;

use WC_Email;
use WC_Order;

defined( 'ABSPATH' ) || exit;

/**
 * Class OrderConfirmationEmail
 *
 * Definerer en tilpasset ordrebekræftelses-e-mail for Administration-pluginet.
 *
 * @package Administration\Components\Integrations\Woocommerce;
 */
class OrderConfirmationEmail extends WC_Email {

    /**
     * Constructor.
     */
    public function __construct() {
        $this->id             = 'administration_order_confirmation';
        $this->title          = __( 'Administration Ordrebekræftelse', 'administration' );
        $this->description    = __( 'Sender en ordrebekræftelse med tilvalg og individuelle valg.', 'administration' );
        $this->template_html  = 'emails/order-confirmation.php';
        $this->template_plain = 'emails/plain/order-confirmation.php';
        $this->template_base  = ADMINISTRATION_PLUGIN_DIR . 'templates/';

        // Triggers for this email
        add_action( 'administration_order_confirmation_notification', array( $this, 'trigger' ), 10, 2 );

        // Call parent constructor
        parent::__construct();

        // Other settings
        $this->recipient = '';
        $this->heading   = $this->get_default_heading();
        $this->subject   = $this->get_default_subject();
    }

    /**
     * Udløser afsendelsen af denne e-mail.
     *
     * @param int       $order_id
     * @param WC_Order  $order
     */
    public function trigger( $order_id, $order = false ) {
        if ( $order_id && ! is_a( $order, 'WC_Order' ) ) {
            $order = wc_get_order( $order_id );
        }

        if ( ! $order ) {
            return;
        }

        $this->object     = $order;
        $this->recipient  = $order->get_billing_email();

        if ( ! $this->is_enabled() || ! $this->get_recipient() ) {
            return;
        }

        $this->send( $this->get_recipient(), $this->get_subject(), $this->get_content(), $this->get_headers(), $this->get_attachments() );
    }

    /**
     * Henter standard e-mail-emne.
     *
     * @return string
     */
    public function get_default_subject() {
        return __( 'Ordrebekræftelse for din ordre #{order_number}', 'administration' );
    }

    /**
     * Henter standard e-mail-overskrift.
     *
     * @return string
     */
    public function get_default_heading() {
        return __( 'Tak for din ordre', 'administration' );
    }

    /**
     * Henter HTML-indholdet til e-mailen.
     *
     * @return string
     */
    public function get_content_html() {
        return wc_get_template_html(
            $this->template_html,
            array(
                'order'         => $this->object,
                'email_heading' => $this->get_heading(),
                'sent_to_admin' => false,
                'plain_text'    => false,
                'email'         => $this,
            ),
            '',
            $this->template_base
        );
    }

    /**
     * Henter tekstindholdet til e-mailen.
     *
     * @return string
     */
    public function get_content_plain() {
        return wc_get_template_html(
            $this->template_plain,
            array(
                'order'         => $this->object,
                'email_heading' => $this->get_heading(),
                'sent_to_admin' => false,
                'plain_text'    => true,
                'email'         => $this,
            ),
            '',
            $this->template_base
        );
    }

    /**
     * Initialiserer indstillingsformularens felter.
     */
    public function init_form_fields() {
        $this->form_fields = array(
            'enabled' => array(
                'title'   => __( 'Aktiver/Deaktiver', 'administration' ),
                'type'    => 'checkbox',
                'label'   => __( 'Aktiver denne e-mail med tilpasset ordrebekræftelse', 'administration' ),
                'default' => 'yes',
            ),
            'subject' => array(
                'title'       => __( 'Emne', 'administration' ),
                'type'        => 'text',
                'description' => sprintf( __( 'Dette kontrollerer e-mail emne. Standard: %s', 'administration' ), $this->get_default_subject() ),
                'default'     => '',
                'placeholder' => '',
            ),
            'heading' => array(
                'title'       => __( 'Overskrift', 'administration' ),
                'type'        => 'text',
                'description' => sprintf( __( 'Dette kontrollerer e-mail overskrift. Standard: %s', 'administration' ), $this->get_default_heading() ),
                'default'     => '',
                'placeholder' => '',
            ),
        );
    }
}
