<?php
namespace Administration\Components\Integrations\MicrosoftGraph;

use WC_Order;
use Administration\Integrations\MicrosoftGraph\API;
use Administration\Integrations\MicrosoftGraph\Auth;
use Administration\Utilities\Logger;

defined( 'ABSPATH' ) || exit;

/**
 * Class OrderSync
 *
 * Håndterer ordrebehandling og opdaterer Microsoft Calendar begivenheder med ordredata.
 *
 * @package Administration\Components\Integrations\MicrosoftGraph;
 */
class OrderSync {

    /**
     * Constructor.
     */
    public function __construct() {
        add_action( 'woocommerce_order_status_completed', array( $this, 'update_calendar_event_with_order' ), 10, 1 );
    }

    /**
     * Opdaterer kalenderbegivenheden med ordredata.
     *
     * @param int $order_id
     */
    public function update_calendar_event_with_order( $order_id ) {
        $order = wc_get_order( $order_id );
        if ( ! $order ) {
            return;
        }

        foreach ( $order->get_items() as $item ) {
            $product = $item->get_product();
            if ( ! $product || 'experience' !== $product->get_type() ) {
                continue;
            }

            // Hent oplevelsesdato
            $experience_date = $item->get_meta( '_experience_date' );
            $event_id        = $this->get_event_id_by_date( $product->get_id(), $experience_date );

            if ( $event_id ) {
                $api = new API( 'https://graph.microsoft.com/v1.0', new \Administration\Integrations\MicrosoftGraph\TokenHandler() );

                // Forbered opdaterede begivenhedsdata
                $event_data = array(
                    'body' => array(
                        'contentType' => 'HTML',
                        'content'     => $this->generate_event_body_content( $order, $item ),
                    ),
                );

                // Opdater begivenheden
                $response = $api->patch( '/me/events/' . $event_id, $event_data );

                if ( ! $response ) {
                    // Log fejl
                    Logger::get_instance()->error( 'Failed to update calendar event.', array( 'order_id' => $order_id, 'event_id' => $event_id ) );
                }
            }
        }
    }

    /**
     * Henter begivenheds-ID baseret på dato.
     *
     * @param int    $product_id
     * @param string $experience_date
     * @return string|false
     */
    private function get_event_id_by_date( $product_id, $experience_date ) {
        $dates = get_post_meta( $product_id, '_experience_dates', true );
        if ( ! is_array( $dates ) ) {
            return false;
        }

        foreach ( $dates as $date ) {
            if ( isset( $date['start_time'], $date['event_id'] ) && $date['start_time'] === $experience_date ) {
                return $date['event_id'];
            }
        }

        return false;
    }

    /**
     * Genererer indhold til begivenhedens body med kundeinformation.
     *
     * @param WC_Order $order
     * @param WC_Order_Item_Product $item
     * @return string
     */
    private function generate_event_body_content( $order, $item ) {
        // Sanitering af kundeoplysninger
        $first_name = sanitize_text_field( $order->get_billing_first_name() );
        $last_name  = sanitize_text_field( $order->get_billing_last_name() );
        $email      = sanitize_email( $order->get_billing_email() );
        $phone      = sanitize_text_field( $order->get_billing_phone() );

        $content = '<p>' . esc_html__( 'Kundeinformation:', 'administration' ) . '</p>';
        $content .= '<ul>';
        $content .= '<li>' . esc_html__( 'Navn: ', 'administration' ) . esc_html( $first_name . ' ' . $last_name ) . '</li>';
        $content .= '<li>' . esc_html__( 'Email: ', 'administration' ) . esc_html( $email ) . '</li>';
        $content .= '<li>' . esc_html__( 'Telefon: ', 'administration' ) . esc_html( $phone ) . '</li>';
        $content .= '</ul>';

        // Tilføj eventuelle tilvalg
        $content .= '<p>' . esc_html__( 'Tilvalg:', 'administration' ) . '</p>';
        $content .= '<ul>';
        // Antager, at tilvalg er gemt som ordrelinjemetafelt
        $tilvalg = $item->get_meta( '_experience_tilvalg' );
        if ( $tilvalg && is_array( $tilvalg ) ) {
            foreach ( $tilvalg as $name => $value ) {
                $content .= '<li>' . esc_html( $name ) . ': ' . esc_html( $value ) . '</li>';
            }
        }
        $content .= '</ul>';

        return $content;
    }
}

// Initialiserer klassen
new OrderProcessing();
