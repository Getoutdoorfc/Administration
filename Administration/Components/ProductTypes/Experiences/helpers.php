<?php

/**
 * Henter produktkategorier med caching.
 *
 * @return array|WP_Error Liste over produktkategorier eller WP_Error ved fejl.
 */
function get_product_categories() {
    // Forsøg at hente kategorier fra cachen
    $categories = get_transient('product_categories');
    
    // Hvis cachen er tom, hent kategorier fra databasen
    if (false === $categories) {
        $categories = get_terms(array(
            'taxonomy'   => 'product_cat',
            'hide_empty' => false,
        ));
        
        // Hvis der ikke er fejl, gem kategorierne i cachen
        if (!is_wp_error($categories)) {
            set_transient('product_categories', $categories, DAY_IN_SECONDS);
        }
    }
    
    return $categories;
}

/**
 * Rydder cachen for produktkategorier.
 */
function clear_product_categories_cache() {
    delete_transient('product_categories');
}

// Hook til at rydde cachen, når en produktkategori opdateres
add_action('edited_product_cat', 'clear_product_categories_cache');
add_action('create_product_cat', 'clear_product_categories_cache');
add_action('delete_product_cat', 'clear_product_categories_cache');
