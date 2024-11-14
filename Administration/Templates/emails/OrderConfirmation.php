<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

echo $email_heading . "\n\n";

echo sprintf( __( 'Tak for din ordre #%s. Nedenfor finder du detaljer om din ordre:', 'administration' ), $order->get_order_number() ) . "\n\n";

// Ordredetaljer
foreach ( $order->get_items() as $item_id => $item ) {
    $product = $item->get_product();
    if ( ! $product ) {
        continue;
    }

    echo $product->get_name() . ' x ' . $item->get_quantity() . "\n";

    // Tilvalg og individuelle valg
    $tilvalg = $item->get_meta( '_experience_tilvalg' );
    if ( $tilvalg ) {
        echo __( 'Tilvalg:', 'administration' ) . "\n";
        foreach ( $tilvalg as $name => $value ) {
            echo ' - ' . esc_html( $name ) . ': ' . esc_html( $value ) . "\n";
        }
    }

    $participants = $item->get_meta( '_experience_participants' );
    if ( $participants ) {
        echo __( 'Deltager Valg:', 'administration' ) . "\n";
        foreach ( $participants as $participant ) {
            echo ' - ' . esc_html( $participant['name'] ) . "\n";
            foreach ( $participant['choices'] as $choice_name => $choice_value ) {
                echo '   * ' . esc_html( $choice_name ) . ': ' . esc_html( $choice_value ) . "\n";
            }
        }
    }

    echo "\n";
}

echo __( 'Vi ser frem til at betjene dig.', 'administration' ) . "\n\n";

echo __( 'Med venlig hilsen,', 'administration' ) . "\n";
echo __( 'Get Indoor Teamet', 'administration' ) . "\n";
