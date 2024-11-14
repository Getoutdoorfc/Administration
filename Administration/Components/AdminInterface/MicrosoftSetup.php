<?php
namespace Administration\Components\AdminInterface;

defined('ABSPATH') || exit;

use Administration\Utilities\Crypto;
use Administration\Utilities\Logger;
use Administration\Components\Integrations\MicrosoftGraph\Auth;
use Administration\Components\AdminInterface\Templates\MicrosoftSetupPage;
use Administration\Components\Utilities\Validation;

class MicrosoftSetup {

    public function __construct() {
        // Register settings
        add_action('admin_init', array($this, 'register_settings'));
        
        // Handle form submission for Microsoft setup
        add_action('admin_post_administration_microsoft_setup', array($this, 'handle_form_submission'));

        // Handle OAuth response
        add_action('admin_init', array($this, 'handle_oauth_response'));
    }

    public function register_settings() {
        register_setting('administration_microsoft_options', 'administration_microsoft_client_id', 'sanitize_text_field');
        register_setting('administration_microsoft_options', 'administration_microsoft_client_secret');
        register_setting('administration_microsoft_options', 'administration_microsoft_tenant_id', 'sanitize_text_field');
    }

    public function render_setup_page() {
        // Check user permission
        if (!current_user_can('manage_options')) {
            wp_die(__('You do not have permission to access this page.', 'administration'));
        }

        // Include template file
        include plugin_dir_path(__FILE__) . 'Templates/MicrosoftSetupPage.php';

        // Instantiate and render the setup page
        $page = new MicrosoftSetupPage();
        $page->render_setup_page();
    }

    public function handle_form_submission() {
        // Check user permission
        if (!current_user_can('manage_options')) {
            wp_die(__('You do not have permission to perform this action.', 'administration'));
        }

        // Handle form submission
        if (isset($_POST['administration_microsoft_submit'])) {
            check_admin_referer('administration_microsoft_options_verify');

            $client_id = sanitize_text_field($_POST['administration_microsoft_client_id'] ?? '');
            $client_secret = sanitize_text_field($_POST['administration_microsoft_client_secret'] ?? '');
            $tenant_id = sanitize_text_field($_POST['administration_microsoft_tenant_id'] ?? '');

            $errors = [];

            // Validate inputs
            if (!Validation::validate_client_id($client_id)) {
                $errors[] = __('Invalid Client ID format.', 'administration');
            }
            if (!empty($client_secret) && !Validation::validate_client_secret($client_secret)) {
                $errors[] = __('Invalid Client Secret format.', 'administration');
            }
            if (!Validation::validate_tenant_id($tenant_id)) {
                $errors[] = __('Invalid Tenant ID format.', 'administration');
            }

            if (!empty($errors)) {
                foreach ($errors as $error) {
                    add_settings_error('administration_microsoft_options', 'settings_error', $error, 'error');
                }
                wp_redirect(add_query_arg('settings-updated', 'false', wp_get_referer()));
                exit;
            }

            // Encrypt client secret before saving
            if (!empty($client_secret)) {
                $encrypted_client_secret = Crypto::encrypt_data($client_secret);
                update_option('administration_microsoft_client_secret', $encrypted_client_secret);
            }

            update_option('administration_microsoft_client_id', $client_id);
            update_option('administration_microsoft_tenant_id', $tenant_id);

            Logger::getInstance()->info('Microsoft credentials updated.', array('user_id' => get_current_user_id()));

            add_settings_error('administration_microsoft_options', 'settings_updated', __('Settings saved.', 'administration'), 'updated');

            // Redirect back to the setup page
            wp_redirect(add_query_arg('settings-updated', 'true', wp_get_referer()));
            exit;
        }
    }

    public function handle_oauth_response() {
        if (isset($_GET['code']) && isset($_GET['state'])) {
            $auth = Auth::getInstance();
            $auth->handleAuthorizationResponse();
        }
    }
}
