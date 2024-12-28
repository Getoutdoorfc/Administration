<?php
namespace Administration\Modules\WordPress\WordPressComponents;

defined( 'ABSPATH' ) || exit;

/**
 * Class YearWheel
 *
 * Placeholder for årshjul-funktionaliteten.
 *
 * @package Administration\Modules\AdminInterface\AdminComponents\YearWheel
 */
class YearWheel {

    /**
     * Viser årshjul-siden.
     */
    public function display_year_wheel_page() {
        echo '<div class="wrap">';
        echo '<h1>' . esc_html__( 'Årshjul', 'administration' ) . '</h1>';
        echo '<p>' . esc_html__( 'Denne funktion er under udvikling.', 'administration' ) . '</p>';
        echo '</div>';
    }
}
