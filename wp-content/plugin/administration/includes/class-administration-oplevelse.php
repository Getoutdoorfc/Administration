<?php
/* class-administration-oplevelse */

if (!defined('ABSPATH')) {
    exit; // Stop direkte adgang
}

class Administration_Oplevelse {
    public function __construct() {
        add_action('woocommerce_product_options_general_product_data', array($this, 'add_custom_fields'));
        add_action('woocommerce_process_product_meta', array($this, 'save_custom_fields'));
        add_action('wp_ajax_save_oplevelse_data', array($this, 'save_oplevelse_data'));
        add_action('wp_ajax_nopriv_save_oplevelse_data', array($this, 'save_oplevelse_data'));
    }

    public function add_custom_fields() {
        global $woocommerce, $post;

        echo '<div class="options_group">';
        woocommerce_wp_text_input(array(
            'id' => '_oplevelse_date',
            'label' => __('Oplevelse Date', 'administration'),
            'placeholder' => 'YYYY-MM-DD',
            'desc_tip' => 'true',
            'description' => __('Enter the date for the oplevelse.', 'administration')
        ));
        echo '</div>';
    }

    public function save_custom_fields($post_id) {
        $oplevelse_date = sanitize_text_field($_POST['_oplevelse_date']);
        if (!empty($oplevelse_date)) {
            update_post_meta($post_id, '_oplevelse_date', $oplevelse_date);
            error_log("Saved oplevelse date for product ID {$post_id}: {$oplevelse_date}");
        } else {
            error_log("Failed to save oplevelse date for product ID {$post_id}: Empty date");
        }
    }

    public function save_oplevelse_data() {
        check_ajax_referer('save_oplevelse_data_nonce', 'nonce');

        $post_id = intval($_POST['post_id']);
        $oplevelse_date = sanitize_text_field($_POST['oplevelse_date']);

        if (!empty($oplevelse_date)) {
            update_post_meta($post_id, '_oplevelse_date', $oplevelse_date);
            wp_send_json_success(array('message' => 'Oplevelse date saved successfully.'));
            error_log("Saved oplevelse date via AJAX for product ID {$post_id}: {$oplevelse_date}");
        } else {
            wp_send_json_error(array('message' => 'Failed to save oplevelse date: Empty date.'));
            error_log("Failed to save oplevelse date via AJAX for product ID {$post_id}: Empty date");
        }
    }
}
?>
