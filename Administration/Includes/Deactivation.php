<?php
namespace Administration\Includes;

use Administration\Core\Managers\LoggerManager;

defined('ABSPATH') || exit;

/**
 * Class Deactivation
 *
 * Håndterer deaktiveringslogik for pluginet, herunder:
 * - Fjernelse af planlagte cron-jobs.
 * - Fjernelse af Microsoft-konstanter fra wp-config.php.
 * - Logning af deaktiveringsstatus.
 */
class Deactivation {

    /**
     * Kører ved deaktivering af pluginet.
     */
    public static function deactivate() {
        LoggerManager::getInstance()->info('Deactivating plugin: clearing scheduled events and removing configuration constants.');

        // Rydder planlagte cron-jobs.
        self::clear_cron_jobs();

        // Fjern Microsoft-konfiguration fra wp-config.php.
        self::remove_config_constants();

        LoggerManager::getInstance()->info('Plugin deactivated successfully.');
    }

    /**
     * Fjerner Microsoft-konstanter fra wp-config.php.
     */
    private static function remove_config_constants() {
        $config_file = ABSPATH . 'wp-config.php';

        if (!file_exists($config_file)) {
            LoggerManager::getInstance()->warning('wp-config.php not found. Skipping constant removal.');
            return;
        }

        $config_content = file_get_contents($config_file);
        if ($config_content === false) {
            LoggerManager::getInstance()->error('Unable to read wp-config.php. Skipping constant removal.');
            return;
        }

        // Fjern konstanterne.
        $patterns = [
            "/define\('MICROSOFT_CLIENT_ID', '.*?'\);\n/",
            "/define\('MICROSOFT_CLIENT_SECRET', '.*?'\);\n/",
            "/define\('MICROSOFT_TENANT_ID', '.*?'\);\n/",
        ];

        $updated_content = preg_replace($patterns, '', $config_content);
        if ($updated_content === null) {
            LoggerManager::getInstance()->error('Error processing wp-config.php for constant removal.');
            return;
        }

        // Overskriv wp-config.php med de opdaterede data.
        if (file_put_contents($config_file, $updated_content) === false) {
            LoggerManager::getInstance()->error('Failed to write updated wp-config.php after removing constants.');
        } else {
            LoggerManager::getInstance()->info('Microsoft constants successfully removed from wp-config.php.');
        }
    }

    /**
     * Rydder planlagte cron-jobs for pluginet.
     */
    private static function clear_cron_jobs() {
        if (wp_clear_scheduled_hook('administration_custom_cron_event')) {
            LoggerManager::getInstance()->info('Scheduled cron events cleared successfully.');
        } else {
            LoggerManager::getInstance()->warning('No scheduled cron events found for clearing.');
        }
    }
}
