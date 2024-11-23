<?php namespace Administration\Components\AdminInterface;
function administration_display_lageroversigt_page() {

    if (!current_user_can('manage_options')) {
        return;
    }

    // Hent alle produkter, inkl. variationer
    $args = array(
        'post_type' => array('product', 'product_variation'),
        'posts_per_page' => -1,
        'post_status' => 'publish',
    );
    $products = get_posts($args);

    // Håndter formularindsendelser for at opdatere lagerantal
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['administration_lager_nonce'])) {
        if (!wp_verify_nonce($_POST['administration_lager_nonce'], 'administration_update_lager')) {
            wp_die(__('Sikkerhedskontrol fejlede. Prøv igen.', 'administration'));
        }

        foreach ($_POST['stock'] as $product_id => $stock_quantity) {
            $stock_quantity = intval($stock_quantity);
            update_post_meta($product_id, '_stock', $stock_quantity);
        }

        echo '<div class="notice notice-success is-dismissible"><p>' . __('Lager opdateret.', 'administration') . '</p></div>';
    }

    // Vis en tabel med lagerinformation
    echo '<div class="wrap">';
    echo '<h1>' . __('Lageroversigt', 'administration') . '</h1>';
    echo '<form method="post">';
    wp_nonce_field('administration_update_lager', 'administration_lager_nonce');
    echo '<table class="wp-list-table widefat fixed striped">';
    echo '<thead><tr><th>' . __('Navn', 'administration') . '</th><th>' . __('SKU', 'administration') . '</th><th>' . __('Lagerantal', 'administration') . '</th></tr></thead>';
    echo '<tbody>';

    foreach ($products as $product) {
        $product_id = $product->ID;
        $product_name = get_the_title($product_id);
        $product_sku = get_post_meta($product_id, '_sku', true);
        $stock_quantity = get_post_meta($product_id, '_stock', true);

        echo '<tr>';
        echo '<td>' . esc_html($product_name) . '</td>';
        echo '<td>' . esc_html($product_sku) . '</td>';
        echo '<td><input type="number" name="stock[' . esc_attr($product_id) . ']" value="' . esc_attr($stock_quantity) . '" /></td>';
        echo '</tr>';
    }

    echo '</tbody>';
    echo '</table>';
    echo '<p><input type="submit" class="button button-primary" value="' . __('Opdater Lager', 'administration') . '" /></p>';
    echo '</form>';
    echo '</div>';
}
