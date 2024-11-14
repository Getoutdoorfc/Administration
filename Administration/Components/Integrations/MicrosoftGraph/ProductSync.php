<?php
namespace Administration\Components\Integrations\MicrosoftGraph;

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class ProductSync {

    public function __construct() {
        add_action( 'save_post_product', array( $this, 'sync_calendar_events' ), 10, 3 );
    }

    public function sync_calendar_events( $post_id, $post, $update ) {
        if ( wp_is_post_revision( $post_id ) || wp_is_post_autosave( $post_id ) ) {
            return;
        }

        if ( ! current_user_can( 'edit_product', $post_id ) ) {
            return;
        }

        $product = wc_get_product( $post_id );
        if ( ! $product || 'experiences' !== $product->get_type() ) {
            return;
        }

        $dates = get_post_meta( $post_id, '_experience_dates', true );
        if ( ! is_array( $dates ) ) {
            return;
        }

        $api = new Administration_Microsoft_Graph_API( 'https://graph.microsoft.com/v1.0', new Administration_Token_Handler() );

        // Hent tidligere gemte datoer
        $old_dates = get_post_meta( $post_id, '_experience_dates', true );
        if ( ! is_array( $old_dates ) ) {
            $old_dates = array();
        }

        // Byg et array af event_id'er fra de nye datoer
        $new_event_ids = array();
        foreach ( $dates as $date ) {
            if ( isset( $date['event_id'] ) ) {
                $new_event_ids[] = $date['event_id'];
            }
        }

        // Sammenlign med gamle datoer for at finde slettede begivenheder
        $deleted_event_ids = array();
        foreach ( $old_dates as $old_date ) {
            if ( isset( $old_date['event_id'] ) && ! in_array( $old_date['event_id'], $new_event_ids ) ) {
                $deleted_event_ids[] = $old_date['event_id'];
            }
        }

        // Slet slettede begivenheder
        foreach ( $deleted_event_ids as $event_id ) {
            $api->delete( '/me/events/' . $event_id );
        }

        foreach ( $dates as &$date ) {
            // Forbered begivenhedsdata
            $event_data = $this->prepare_event_data( $date, $product );

            // Tjek om event_id eksisterer
            if ( isset( $date['event_id'] ) && ! empty( $date['event_id'] ) ) {
                // Opdater eksisterende begivenhed
                $response = $api->patch( '/me/events/' . $date['event_id'], $event_data );
            } else {
                // Opret ny begivenhed
                $response = $api->post( '/me/events', $event_data );
                if ( isset( $response['id'] ) ) {
                    $date['event_id'] = $response['id'];
                }
            }
        }

        // Opdater datoer med event_id'er
        update_post_meta( $post_id, '_experience_dates', $dates );

        // Efter opdatering eller oprettelse af begivenheder, slet cachen for kalenderkategorier
        delete_transient( 'administration_calendar_categories' );
    }

    private function prepare_event_data( $date, $product ) {
        $date_helper = new Administration_Date_Helper();

        // Konverter start- og sluttidspunkt til ISO 8601 format
        $start_time = $date_helper->to_iso8601( $date['start_time'] );
        $end_time   = $date_helper->calculate_end_time( $date['start_time'], $date['duration'] );

        // Forbered begivenhedsdata
        $event_data = array(
            'subject' => $product->get_name(),
            'body' => array(
                'contentType' => 'HTML',
                'content'     => isset( $date['notes'] ) ? $date['notes'] : '',
            ),
            'start' => array(
                'dateTime' => $start_time,
                'timeZone' => 'UTC',
            ),
            'end' => array(
                'dateTime' => $end_time,
                'timeZone' => 'UTC',
            ),
            'location' => array(
                'displayName' => 'Get Outdoor',
            ),
            'categories' => array(
                $product->get_meta( '_category_color' ), // Antager at kategorifarve er gemt som meta
            ),
        );

        return $event_data;
    }
}

new Administration_Product_Sync();
