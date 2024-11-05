<?php
if (!defined('ABSPATH')) {
    exit; // Stop direkte adgang
}

echo '<div class="wrap">';
echo '<h1>' . esc_html__('Administration Dashboard', 'administration') . '</h1>';
echo '<p>' . esc_html__('Velkommen til Administration pluginet.', 'administration') . '</p>';
echo '</div>';
