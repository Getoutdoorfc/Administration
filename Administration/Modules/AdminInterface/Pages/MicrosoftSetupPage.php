<?php
namespace Administration\Components\AdminInterface\Templates;

use Administration\Components\AdminInterface\AdminNotices;
use Administration\Components\AdminInterface\Templates\MicrosoftSetupGuide;
use Administration\Components\Utilities\ConfigHandler;
use Administration\Components\Utilities\Logger;

defined('ABSPATH') || exit;

/**
 * Class MicrosoftSetupPage
 *
 * Renderer Microsoft Opsætningssiden i admin-panelet.
 */
class MicrosoftSetupPage {

    /**
     * Renderer opsætningssiden for Microsoft.
     */
    public function render_setup_page() {
        if (!current_user_can('manage_options')) {
            wp_die(__('You do not have permission to access this page.', 'administration'));
        }

        Logger::getInstance()->info("Rendering Microsoft Setup Page.");

        // Hent credentials fra ConfigHandler
        $client_id = ConfigHandler::get_config('microsoft_client_id') ?? '';
        $client_secret = ConfigHandler::get_config('microsoft_client_secret') ?? '';
        $tenant_id = ConfigHandler::get_config('microsoft_tenant_id') ?? '';
        $credentials_saved = !empty($client_id) && !empty($client_secret) && !empty($tenant_id);

        // Indlæs styles
        $this->enqueue_assets();

        ?>
        <div class="wrap">
            <h1><?php esc_html_e('Microsoft Setup', 'administration'); ?></h1>

            <!-- Statusmeddelelse -->
            <div id="status-message">
                <?php $this->render_status_message($credentials_saved); ?>
            </div>

            <!-- Dynamisk vejledning -->
            <div class="setup-guide">
                <?php $this->render_setup_guide($credentials_saved); ?>
            </div>

            <!-- Formular til indtastning af credentials -->
            <?php $this->render_credentials_form($client_id, $client_secret, $tenant_id); ?>

            <!-- Dynamisk sektion til login-knap -->
            <div id="microsoft-login-section">
                <?php $this->render_login_section($credentials_saved); ?>
            </div>
        </div>
        <?php

        Logger::getInstance()->info("Microsoft Setup Page rendered successfully.");
    }

    /**
     * Indlæser styles og scripts.
     */
    private function enqueue_assets() {
        wp_enqueue_style(
            'administration-admin-interface-css',
            plugins_url('Assets/css/AdminInterface.css', __DIR__),
            [],
            '1.0.0'
        );
    }

    /**
     * Renderer statusmeddelelsen.
     *
     * @param bool $credentials_saved
     */
    private function render_status_message($credentials_saved) {
        if ($credentials_saved) {
            AdminNotices::display_message(__('Credentials are saved. You can now log in with Microsoft.', 'administration'), 'updated');
        } else {
            AdminNotices::display_message(__('Please enter your credentials and save them to enable login.', 'administration'), 'warning');
        }
    }

    /**
     * Renderer dynamisk vejledning.
     *
     * @param bool $credentials_saved
     */
    private function render_setup_guide($credentials_saved) {
        $guide = new MicrosoftSetupGuide();
        $guide->render_guide($credentials_saved);
    }

    /**
     * Renderer credentials-formularen.
     *
     * @param string $client_id
     * @param string $client_secret
     * @param string $tenant_id
     */
    private function render_credentials_form($client_id, $client_secret, $tenant_id) {
        ?>
        <form method="post" action="">
            <?php wp_nonce_field('administration_microsoft_options_verify', 'security'); ?>
            <table class="form-table">
                <tr>
                    <th scope="row">
                        <label for="administration_microsoft_client_id"><?php esc_html_e('Client ID', 'administration'); ?></label>
                    </th>
                    <td>
                        <input name="administration_microsoft_client_id" type="text" id="administration_microsoft_client_id" 
                               value="<?php echo esc_attr($client_id); ?>" class="regular-text" />
                    </td>
                </tr>
                <tr>
                    <th scope="row">
                        <label for="administration_microsoft_client_secret"><?php esc_html_e('Client Secret', 'administration'); ?></label>
                    </th>
                    <td>
                        <input name="administration_microsoft_client_secret" type="text" id="administration_microsoft_client_secret" 
                               value="<?php echo esc_attr($client_secret); ?>" class="regular-text" />
                    </td>
                </tr>
                <tr>
                    <th scope="row">
                        <label for="administration_microsoft_tenant_id"><?php esc_html_e('Tenant ID', 'administration'); ?></label>
                    </th>
                    <td>
                        <input name="administration_microsoft_tenant_id" type="text" id="administration_microsoft_tenant_id" 
                               value="<?php echo esc_attr($tenant_id); ?>" class="regular-text" />
                    </td>
                </tr>
            </table>

            <!-- Save-knap -->
            <?php submit_button(__('Save Settings', 'administration')); ?>
        </form>
        <?php

        // Gemmer værdierne, hvis formularen indsendes
        $this->handle_form_submission();
    }

    /**
     * Håndterer formularindsendelse for at gemme credentials.
     */
    private function handle_form_submission() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (!isset($_POST['security']) || !wp_verify_nonce($_POST['security'], 'administration_microsoft_options_verify')) {
                AdminNotices::add_dynamic_notice(__('Invalid nonce. Please try again.', 'administration'), 'error');
                return;
            }

            // Sanitér og gem credentials
            $client_id = sanitize_text_field($_POST['administration_microsoft_client_id'] ?? '');
            $client_secret = sanitize_text_field($_POST['administration_microsoft_client_secret'] ?? '');
            $tenant_id = sanitize_text_field($_POST['administration_microsoft_tenant_id'] ?? '');

            if (empty($client_id) || empty($client_secret) || empty($tenant_id)) {
                AdminNotices::add_dynamic_notice(__('All fields are required.', 'administration'), 'error');
                return;
            }

            // Gem med ConfigHandler
            if (
                ConfigHandler::set_constant('MICROSOFT_CLIENT_ID', $client_id) &&
                ConfigHandler::set_constant('MICROSOFT_CLIENT_SECRET', $client_secret) &&
                ConfigHandler::set_constant('MICROSOFT_TENANT_ID', $tenant_id)
            ) {
                AdminNotices::add_dynamic_notice(__('Settings saved successfully.', 'administration'), 'updated');
            } else {
                AdminNotices::add_dynamic_notice(__('Failed to save settings. Check logs for details.', 'administration'), 'error');
            }
        }
    }

    /**
     * Renderer login-sektionen.
     *
     * @param bool $credentials_saved
     */
    private function render_login_section($credentials_saved) {
        if ($credentials_saved) {
            ?>
            <hr>
            <h2><?php esc_html_e('Log in with Microsoft', 'administration'); ?></h2>
            <p><?php esc_html_e('Click the button below to log in with your Microsoft account.', 'administration'); ?></p>
            <a href="<?php echo esc_url('#'); ?>" class="button button-primary">
                <?php esc_html_e('Log in with Microsoft', 'administration'); ?>
            </a>
            <?php
        } else {
            ?>
            <hr>
            <h2><?php esc_html_e('Login unavailable', 'administration'); ?></h2>
            <p><?php esc_html_e('You need to save your credentials before logging in with Microsoft.', 'administration'); ?></p>
            <?php
        }
    }
}
