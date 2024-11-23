<?php
namespace Administration\Components\Integrations\MicrosoftGraph;

use Administration\Components\Utilities\Logger;

defined('ABSPATH') || exit;

/**
 * Class OrderSync
 *
 * Håndterer synkronisering af ordreafslutninger med Microsoft Kalender.
 *
 * @package Administration\Components\Integrations\MicrosoftGraph
 */
class OrderSync {

    /**
     * Constructor.
     * Tilføjer handling til at synkronisere ordreafslutninger.
     */
    public function __construct() {
        add_action('woocommerce_order_status_completed', array($this, 'sync_order_completion'));
    }

    /**
     * Synkroniserer ordreafslutning med Microsoft Kalender.
     *
     * @param int $order_id Order ID.
     */
    public function sync_order_completion($order_id) {
        $order = wc_get_order($order_id);
        if (!$order) {
            return;
        }

        $api = new API();
        $body = array(
            'subject' => 'Order Completed: ' . $order->get_order_number(),
            'body' => array(
                'contentType' => 'HTML',
                'content' => 'Order ' . $order->get_order_number() . ' has been completed.',
            ),
            'start' => array(
                'dateTime' => date('c'),
                'timeZone' => 'UTC',
            ),
            'end' => array(
                'dateTime' => date('c', strtotime('+1 hour')),
                'timeZone' => 'UTC',
            ),
        );

        $response = $api->post('/me/events', $body);
        if ($response === false) {
            Logger::getInstance()->error('Failed to sync order completion to Microsoft Calendar.', array('order_id' => $order_id));
        } else {
            Logger::getInstance()->info('Order completion synced to Microsoft Calendar.', array('order_id' => $order_id));
        }
    }
}

new OrderSync();
