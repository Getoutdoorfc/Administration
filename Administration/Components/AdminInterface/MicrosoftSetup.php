<?php
namespace Administration\Components\AdminInterface;

use Administration\Utilities\Crypto;
use Administration\Utilities\Logger;
use Administration\Components\Integrations\MicrosoftGraph\Auth;
use Administration\Components\Utilities\Validation;
use Administration\Components\AdminInterface\Templates\MicrosoftSetupPage;

defined('ABSPATH') || exit;

class MicrosoftSetup {

    public function __construct() {
        add_action('admin_init', array($this, 'register_settings'));
        add_action('admin_post_administration_microsoft_setup', array($this, 'handle_form_submission'));
        add_action('admin_init', array($this, 'handle_oauth_response'));
    }

    private function check_permissions() {
        if (!current_user_can('manage_options')) {
            wp_die(__('You do not have permission to perform this action.', 'administration'));
        }
    }

    public function register_settings() {
        register_setting('administration_microsoft_options', 'administration_microsoft_client_id', 'sanitize_text_field');
        register_setting('administration_microsoft_options', 'administration_microsoft_client_secret');
        register_setting('administration_microsoft_options', 'administration_microsoft_tenant_id', 'sanitize_text_field');
    }

    public function render_setup_page() {
        $this->check_permissions();
        $page = new MicrosoftSetupPage();
        $page->render_setup_page();
    }

    public function handle_form_submission() {
        $this->check_permissions();

        if (isset($_POST['administration_microsoft_submit']) && check_admin_referer('administration_microsoft_options_verify')) {
            $client_id = sanitize_text_field($_POST['administration_microsoft_client_id'] ?? '');
            $client_secret = sanitize_text_field($_POST['administration_microsoft_client_secret'] ?? '');
            $tenant_id = sanitize_text_field($_POST['administration_microsoft_tenant_id'] ?? '');

            $errors = $this->validate_inputs($client_id, $client_secret, $tenant_id);

            if (!empty($errors)) {
                foreach ($errors as $error) {
                    add_settings_error('administration_microsoft_options', 'settings_error', $error, 'error');
                }
                wp_redirect(add_query_arg('settings-updated', 'false', wp_get_referer()));
                exit;
            }

            if (!empty($client_secret)) {
                $encrypted_client_secret = Crypto::encrypt_data($client_secret);
                update_option('administration_microsoft_client_secret', $encrypted_client_secret);
            }
            update_option('administration_microsoft_client_id', $client_id);
            update_option('administration_microsoft_tenant_id', $tenant_id);

            Logger::getInstance()->info('Microsoft credentials updated.', array('user_id' => get_current_user_id()));
            add_settings_error('administration_microsoft_options', 'settings_updated', __('Settings saved.', 'administration'), 'updated');

            wp_redirect(add_query_arg('settings-updated', 'true', wp_get_referer()));
            exit;
        }
    }

    private function validate_inputs($client_id, $client_secret, $tenant_id) {
        $errors = [];
        if (!Validation::validate_client_id($client_id)) {
            $errors[] = __('Invalid Client ID format.', 'administration');
        }
        if (!empty($client_secret) && !Validation::validate_client_secret($client_secret)) {
            $errors[] = __('Invalid Client Secret format.', 'administration');
        }
        if (!Validation::validate_tenant_id($tenant_id)) {
            $errors[] = __('Invalid Tenant ID format.', 'administration');
        }
        return $errors;
    }

    public function handle_oauth_response() {
        if (isset($_GET['code']) && isset($_GET['state'])) {
            $auth = Auth::getInstance();
            $auth->handleAuthorizationResponse();
        }
    }
}
?>
