<?php
namespace Administration\Includes;

defined( 'ABSPATH' ) || exit;

/**
 * Class Deactivation
 *
 * Håndterer deaktiveringslogik for pluginet.
 *
 * @package Administration\Includes
 */
class Deactivation {

    /**
     * Kører ved deaktivering af pluginet.
     */
    public static function deactivate() {
        // Ryd midlertidige data eller stop planlagte begivenheder.
        wp_clear_scheduled_hook( 'administration_custom_cron_event' );
    }
}
