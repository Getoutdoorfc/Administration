<?php
namespace Administration\Modules\MicroSoft\MicroSoftServices;

use Administration\Core\Managers\LoggerManager;
use Administration\Modules\MicroSoft\MicroSoftCore\MsRestApiHandler;
use WC_Product;
use WP_Post;

defined('ABSPATH') || exit;

/**
 * Class MsProductSync
 *
 * Håndterer synkronisering af produktopdateringer med Microsoft Kalender.
 *
 * @package Administration\Modules\MicroSoft\MicroSoftServices
 * 
 */
class MsProductSync {

    /**
     * Constructor.
     * Tilføjer handling til at synkronisere produktopdateringer.
     */
    public function __construct() {
        add_action('save_post_product', array($this, 'sync_product_update'), 10, 3);
    }

    /**
     * Synkroniserer produktopdateringer med Microsoft Kalender.
     *
     * @param int      $post_id Post ID.
     * @param \WP_Post $post    Post objekt.
     * @param bool     $update  Om dette er en opdatering af et eksisterende indlæg.
     */
    public function sync_product_update($post_id, $post, $update) {
        if (wp_is_post_revision($post_id) || wp_is_post_autosave($post_id)) {
            return;
        }

        if (!current_user_can('edit_product', $post_id)) {
            return;
        }

        $product = wc_get_product($post_id);
        if (!$product) {
            return;
        }

        $api = new MsRestApiHandler();
        $body = array(
            'subject' => 'Product Updated: ' . $product->get_name(),
            'body' => array(
                'contentType' => 'HTML',
                'content' => 'Product ' . $product->get_name() . ' has been updated.',
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
            LoggerManager::getInstance()->error('Failed to sync product update to Microsoft Calendar.', array('product_id' => $post_id));
        } else {
            LoggerManager::getInstance()->info('Product update synced to Microsoft Calendar.', array('product_id' => $post_id));
        }
    }
}

new MsProductSync();
