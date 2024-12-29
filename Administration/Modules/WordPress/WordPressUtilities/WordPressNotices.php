<?php
namespace Administration\Modules\WordPress\WordPressUtilities;

use Administration\Core\Managers\LoggerManager;

defined('ABSPATH') || exit;

/**
 * Class AdminNotices
 *
 * Håndterer og viser administrative meddelelser i WordPress-dashboardet.
 * 
 * @package Administration\Modules\WordPress\WordPressUtilities
 * @since WordPress 1.0.0
 * @version 1.0.0
 * 
 */
class WordPressNotices {

    /**
     * Constructor.
     * Tilføjer handling til at vise admin-meddelelser.
     */
    public function __construct() {
        add_action('admin_notices', [$this, 'display_admin_notices']);
    }

    /**
     * Viser admin-meddelelser relateret til pluginet.
     */
    public function display_admin_notices() {
        LoggerManager::getInstance()->info('Displaying admin notices.');

        $errors = get_settings_errors('administration_microsoft_options');
        if (!empty($errors)) {
            foreach ($errors as $error) {
                $class = 'notice-' . ($error['type'] === 'error' ? 'error' : 'updated');
                printf(
                    '<div class="%s notice is-dismissible"><p>%s</p></div>',
                    esc_attr($class),
                    esc_html($error['message'])
                );
                LoggerManager::getInstance()->info("Displayed notice: {$error['message']}");
            }
        }
    }

    /**
     * Henter en standardbesked baseret på nøgle.
     *
     * @param string $key Beskedens nøgle.
     * @return string Beskeden.
     */
    public static function get_message(string $key): string {
        $messages = [
            'missing_credentials' => __('Please provide all required Microsoft credentials.', 'administration'),
            'setup_success' => __('Settings saved successfully.', 'administration'),
            'auth_failed' => __('Authorization failed. Please try again.', 'administration'),
            'reset_complete' => __('Settings have been reset successfully.', 'administration'),
            'unknown_error' => __('An unknown error occurred. Please check the logs.', 'administration'),
        ];

        return $messages[$key] ?? __('An unspecified error occurred.', 'administration');
    }

    /**
     * Tilføjer en dynamisk besked til visning.
     *
     * @param string $message Beskeden, der skal vises.
     * @param string $type    Beskedtype: 'error', 'updated', 'info'.
     */
    public static function add_dynamic_notice(string $message, string $type = 'updated'): void {
        LoggerManager::getInstance()->info("Adding dynamic notice: {$message}");
        add_settings_error('administration_microsoft_options', uniqid('notice_'), $message, $type);
    }

    /**
     * Viser en besked direkte på siden.
     *
     * @param string $message Beskeden, der skal vises.
     * @param string $type    Beskedtype: 'error', 'updated', 'info'.
     */
    public static function display_message(string $message, string $type = 'updated'): void {
        $class = 'notice-' . ($type === 'error' ? 'error' : 'updated');
        printf(
            '<div class="%s notice is-dismissible"><p>%s</p></div>',
            esc_attr($class),
            esc_html($message)
        );
        LoggerManager::getInstance()->info("Displayed direct message: {$message}");
    }

    /**
     * Tilføjer en succesmeddelelse ved nulstilling.
     */
    public static function add_reset_notice(): void {
        self::add_dynamic_notice(self::get_message('reset_complete'), 'updated');
    }

    /**
     * Helper-funktion til at rense tidligere beskeder.
     */
    public static function clear_previous_notices(): void {
        global $wp_settings_errors;
        if (isset($wp_settings_errors['administration_microsoft_options'])) {
            unset($wp_settings_errors['administration_microsoft_options']);
        }
        LoggerManager::getInstance()->info('Cleared previous notices.');
    }
}

// Initialiser klassen
new WordPressNotices();
