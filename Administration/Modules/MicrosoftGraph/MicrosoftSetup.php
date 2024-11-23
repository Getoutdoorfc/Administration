<?php  
namespace Administration\Components\AdminInterface;

use Administration\Components\AdminInterface\Templates\MicrosoftSetupPage;
use Administration\Components\Utilities\Validation;
use Administration\Components\Utilities\Logger;
use Administration\Components\Utilities\ConfigHandler;
AdminInterfaceValidator
defined('ABSPATH') || exit;

/**
 * Class MicrosoftSetup
 *
 * Håndterer Microsoft Opsætningssiden i WordPress admin-dashboardet.
 */
class MicrosoftSetup {

    /**
     * Constructor
     * Registrerer admin-menuen og AJAX-handleren.
     */
    public function __construct() {
        add_action('admin_menu', [$this, 'add_setup_page']);
        add_action('wp_ajax_save_microsoft_settings', [$this, 'handle_ajax_save_settings']);
    }

    /**
     * Tilføjer Microsoft Setup-siden til WordPress admin-menuen.
     */
    public function add_setup_page() {
        add_submenu_page(
            'options-general.php',
            __('Microsoft Setup', 'administration'),
            __('Microsoft Setup', 'administration'),
            'manage_options',
            'administration-microsoft-setup',
            [$this, 'render_setup_page']
        );
    }

    /**
     * Renderer opsætningssiden.
     */
    public function render_setup_page() {
        try {
            $client_id = ConfigHandler::get_config('microsoft_client_id');
            $tenant_id = ConfigHandler::get_config('microsoft_tenant_id');
            $client_secret = ConfigHandler::get_config('microsoft_client_secret');

            $credentials_saved = !empty($client_id) && !empty($client_secret) && !empty($tenant_id);
            Logger::getInstance()->info("Rendering Microsoft Setup Page. Credentials saved: " . ($credentials_saved ? 'Yes' : 'No'));

            $setup_page = new MicrosoftSetupPage();
            $setup_page->render_setup_page($client_id, $client_secret, $tenant_id, $credentials_saved);
        } catch (\Exception $e) {
            Logger::getInstance()->error("Error rendering setup page: " . $e->getMessage());
            echo '<div class="error"><p>' . esc_html__('An error occurred while loading the setup page. Please try again.', 'administration') . '</p></div>';
        }
    }

    /**
     * Håndterer gemning af Microsoft-indstillinger via AJAX.
     */
    public function handle_ajax_save_settings() {
        try {
            if (!isset($_POST['security']) || !wp_verify_nonce($_POST['security'], 'administration_microsoft_options_verify')) {
                AdminNotices::add_dynamic_notice(__('Invalid nonce. Please refresh and try again.', 'administration'), 'error');
                wp_send_json_error(['message' => __('Nonce validation failed.', 'administration')]);
            }

            if (!current_user_can('manage_options')) {
                wp_send_json_error(['message' => __('Permission denied.', 'administration')]);
            }

            $client_id = $this->validate_guid($_POST['administration_microsoft_client_id'] ?? '', 'Client ID');
            $tenant_id = $this->validate_guid($_POST['administration_microsoft_tenant_id'] ?? '', 'Tenant ID');
            $client_secret = $this->sanitize_client_secret($_POST['administration_microsoft_client_secret'] ?? '');

            if (!$client_id || !$tenant_id || !$client_secret) {
                wp_send_json_error(['message' => __('Invalid or missing credentials.', 'administration')]);
            }

            ConfigHandler::set_constant('MICROSOFT_CLIENT_ID', $client_id);
            ConfigHandler::set_constant('MICROSOFT_CLIENT_SECRET', $client_secret);
            ConfigHandler::set_constant('MICROSOFT_TENANT_ID', $tenant_id);

            Logger::getInstance()->info("Microsoft credentials saved successfully.");
            wp_send_json_success(['message' => __('Settings saved successfully.', 'administration')]);
        } catch (\Exception $e) {
            Logger::getInstance()->error("Error in AJAX handler: " . $e->getMessage());
            wp_send_json_error(['message' => __('An unexpected error occurred. Please try again.', 'administration')]);
        }
    }

    /**
     * Validerer GUID-format.
     *
     * @param string $guid Input GUID.
     * @param string $field_name Feltets navn til logging.
     * @return string|false Gyldigt GUID eller false ved fejl.
     */
    private function validate_guid($guid, $field_name) {
        $guid = trim($guid);
        if (preg_match('/^[0-9a-fA-F]{8}-[0-9a-fA-F]{4}-[0-9a-fA-F]{4}-[0-9a-fA-F]{4}-[0-9a-fA-F]{12}$/', $guid)) {
            return $guid;
        }
        Logger::getInstance()->error("$field_name validation failed: $guid");
        return false;
    }

    /**
     * Saniterer og validerer Client Secret.
     *
     * @param string $client_secret Input Client Secret.
     * @return string|false Gyldigt Client Secret eller false ved fejl.
     */
    private function sanitize_client_secret($client_secret) {
        $client_secret = trim($client_secret);
        if (!empty($client_secret)) {
            return $client_secret;
        }
        Logger::getInstance()->error("Client Secret validation failed.");
        return false;
    }
}

new MicrosoftSetup();
