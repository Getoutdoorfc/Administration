<?php
/* class-administration-order-handler */

if (!defined('ABSPATH')) {
    exit; // Stop direkte adgang
}

class Administration_Order_Handler {
    private $msgraph;

    public function __construct() {
        $this->msgraph = new Administration_MSGraph_Auth();
        add_action('woocommerce_order_status_completed', array($this, 'handle_order_completed'), 10, 1);
        add_action('woocommerce_order_status_changed', array($this, 'handle_order_status_change'), 10, 4);
    }

    // Håndterer WooCommerce ordrer, når de er markeret som gennemført
    public function handle_order_completed($order_id) {
        // Sørg for, at WooCommerce er aktivt og wc_get_order er tilgængelig
        if (!function_exists('wc_get_order')) {
            error_log('[Error] WooCommerce is not active.');
            return;
        }

        $order = wc_get_order($order_id);
        if (!$order) {
            error_log('[Error] Order not found for order ID: ' . $order_id);
            return;
        }

        $sku = '';
        $product_id = null;
        foreach ($order->get_items() as $item) {
            $product = $item->get_product();
            if ($product) {
                $sku = $product->get_sku();
                $product_id = $product->get_id();
                break; // Vi går ud fra, at vi kun har brug for første produkt
            }
        }

        if (!$sku || !$product_id) {
            error_log('[Error] No SKU found in the order or product ID is missing for order ID: ' . $order_id);
            return;
        }

        $dates = get_post_meta($product_id, '_experience_dates', true);
        $duration = get_post_meta($product_id, '_experience_duration', true) ?: 3;
        $customer_name = $order->get_billing_first_name() . ' ' . $order->get_billing_last_name();
        $customer_email = $order->get_billing_email();
        $customer_phone = $order->get_billing_phone();
        $order_number = $order->get_order_number();
        $participants = $order->get_item_meta($item->get_id(), 'participants', true) ?: 1;
        $additional_options = $item->get_meta('additional_options');

        if (!$dates) {
            error_log('[Error] No dates found for product ID: ' . $product_id);
            return;
        }

        $access_token = $this->msgraph->get_access_token();

        // Log access token status
        error_log('[Info] Access token received in handle_order_completed: ' . ($access_token ? 'Token received' : 'No token available'));

        foreach (array_map('trim', explode(',', $dates)) as $date_string) {
            try {
                $datetime = DateTime::createFromFormat('Y-m-d\TH:i:s', $date_string);
                if (!$datetime) {
                    throw new Exception('Invalid date/time format');
                }
                $start_date_time = $datetime->format('Y-m-d\TH:i:s');
                $end_date_time = $datetime->modify("+{$duration} hours")->format('Y-m-d\TH:i:s');

                // Hent eksisterende begivenhed
                $existing_events_url = "https://graph.microsoft.com/v1.0/users/kontakt@getoutdoor.dk/calendar/events?" .
                    "$filter=subject eq 'Experience SKU: " . $sku . "' and start/dateTime eq '" . $start_date_time . "'";
                $existing_events_response = wp_remote_get($existing_events_url, array(
                    'headers' => array(
                        'Authorization' => 'Bearer ' . $access_token,
                        'Content-Type' => 'application/json'
                    )
                ));

                if (is_wp_error($existing_events_response)) {
                    error_log('[Error] Failed to check existing events: ' . $existing_events_response->get_error_message());
                    continue;
                }

                $existing_events_body = json_decode(wp_remote_retrieve_body($existing_events_response), true);
                if (empty($existing_events_body['value'])) {
                    error_log('[Error] No existing event found for SKU: ' . $sku . ' at ' . $start_date_time . '. Skipping update.');
                    continue;
                }

                $event_id = $existing_events_body['value'][0]['id'];
                $current_body_content = $existing_events_body['value'][0]['body']['content'];

                // Tilføj kundedata til eksisterende begivenhedens body
                $updated_content = $current_body_content . '<hr>' .
                                  '<p><strong>New Participant Added:</strong></p>' .
                                  '<p>Customer: ' . $customer_name . '</p>' .
                                  '<p>Email: ' . $customer_email . '</p>' .
                                  '<p>Phone: ' . $customer_phone . '</p>' .
                                  '<p>Order Number: ' . $order_number . '</p>' .
                                  '<p>Participants: ' . $participants . '</p>' .
                                  ($additional_options ? '<p>Additional Options: ' . $additional_options . '</p>' : '');

                // Opdater begivenhedens body
                $update_event_url = "https://graph.microsoft.com/v1.0/users/kontakt@getoutdoor.dk/calendar/events/" . $event_id;
                $update_event_response = wp_remote_patch($update_event_url, array(
                    'headers' => array(
                        'Authorization' => 'Bearer ' . $access_token,
                        'Content-Type' => 'application/json'
                    ),
                    'body' => json_encode(array(
                        'body' => array(
                            'contentType' => 'HTML',
                            'content' => $updated_content
                        )
                    ))
                ));

                if (is_wp_error($update_event_response)) {
                    error_log('[Error] Failed to update event: ' . $update_event_response->get_error_message());
                } else {
                    error_log('[Success] Event updated successfully for SKU: ' . $sku);
                }
            } catch (Exception $e) {
                error_log('[Error] Failed to process date: ' . $date_string . '. Message: ' . $e->getMessage());
            }
        }
    }

    public function handle_order_status_change($order_id, $old_status, $new_status, $order) {
        error_log("Order status changed: Order ID {$order_id}, from {$old_status} to {$new_status}");

        // Eksempel på asynkron behandling ved hjælp af WP-Cron
        if ($new_status === 'completed') {
            if (!wp_next_scheduled('administration_process_order', array($order_id))) {
                wp_schedule_single_event(time() + 60, 'administration_process_order', array($order_id));
                error_log("Scheduled async processing for Order ID {$order_id}");
            }
        }
    }

    public function process_order($order_id) {
        $order = wc_get_order($order_id);
        if (!$order) {
            error_log("Failed to retrieve Order ID {$order_id}");
            return;
        }

        // Behandl ordren her
        error_log("Processing Order ID {$order_id}");

        // Eksempel på integration med Microsoft Graph API
        $msgraph_auth = new Administration_MSGraph_Auth();
        $access_token = $msgraph_auth->get_access_token();
        if (!$access_token) {
            error_log("Failed to retrieve access token for Order ID {$order_id}");
            return;
        }

        // Udfør API-kald her
        // ...

        error_log("Completed processing for Order ID {$order_id}");
    }
}

// Hook til asynkron ordrebehandling
add_action('administration_process_order', array('Administration_Order_Handler', 'process_order'));
?>
