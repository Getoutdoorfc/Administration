<?php
namespace Administration\Includes;

defined( 'ABSPATH' ) || exit;

/**
 * Class Deactivation
 *
 * Håndterer deaktiveringslogik for pluginet.
 */
class Deactivation {
    /**
     * Kører ved deaktivering af pluginet.
     */
    public static function deactivate() {
        // Rydder planlagte cron-jobs.
        wp_clear_scheduled_hook( 'administration_custom_cron_event' );

        // Log deaktivering.
        error_log( 'Administration plugin deactivated: scheduled events cleared.' );
    }
}
