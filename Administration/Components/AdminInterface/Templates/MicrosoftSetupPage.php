<?php
namespace Administration\Components\AdminInterface\Templates;

use Administration\Components\Integrations\MicrosoftGraph\Auth;

defined('ABSPATH') || exit;

/**
 * Class MicrosoftSetupPage
 *
 * Håndterer visningen af Microsoft Opsætningssiden i admin-panelet.
 *
 * @package Administration\Components\AdminInterface\Templates
 */
class MicrosoftSetupPage {

    public function render_setup_page() {
        if (!current_user_can('manage_options')) {
            wp_die(__('You do not have permission to access this page.', 'administration'));
        }

        // Enqueue scripts and styles
        wp_enqueue_script('administration-admin-interface-js', plugin_dir_url(__DIR__) . '../../Assets/js/AdminInterface.js', array('jquery'), '1.0', true);
        wp_enqueue_style('administration-admin-interface-css', plugin_dir_url(__DIR__) . '../../Assets/css/AdminInterface.css', array(), '1.0');

        // Retrieve settings
        $client_id = get_option('administration_microsoft_client_id', '');
        $tenant_id = get_option('administration_microsoft_tenant_id', '');
        $credentials_saved = !empty($client_id) && !empty($tenant_id);

        // Capture settings errors
        $errors = get_settings_errors('administration_microsoft_options');
        $field_errors = array();
        if (!empty($errors)) {
            foreach ($errors as $error) {
                $field_errors[] = $error['message'];
            }
        }

        ?>
        <div class="wrap">
            <h1><?php esc_html_e('Microsoft Opsætning', 'administration'); ?></h1>
            <?php settings_errors('administration_microsoft_options'); ?>

            <form method="post" action="<?php echo esc_url(admin_url('admin-post.php')); ?>">
                <?php wp_nonce_field('administration_microsoft_options_verify'); ?>
                <input type="hidden" name="action" value="administration_microsoft_setup">

                <table class="form-table">
                    <?php
                    $fields = [
                        'client_id' => [
                            'label'       => __('Client ID', 'administration'),
                            'type'        => 'text',
                            'value'       => $client_id,
                            'description' => __('Found in Azure Portal under your application settings.', 'administration'),
                        ],
                        'client_secret' => [
                            'label'       => __('Client Secret', 'administration'),
                            'type'        => 'password',
                            'value'       => '',
                            'placeholder' => __('Enter or update Client Secret', 'administration'),
                        ],
                        'tenant_id' => [
                            'label' => __('Tenant ID', 'administration'),
                            'type'  => 'text',
                            'value' => $tenant_id,
                        ],
                    ];

                    foreach ($fields as $key => $field) :
                        ?>
                        <tr>
                            <th scope="row">
                                <label for="administration_microsoft_<?php echo esc_attr($key); ?>">
                                    <?php echo esc_html($field['label']); ?>
                                </label>
                            </th>
                            <td>
                                <input
                                    name="administration_microsoft_<?php echo esc_attr($key); ?>"
                                    type="<?php echo esc_attr($field['type']); ?>"
                                    id="administration_microsoft_<?php echo esc_attr($key); ?>"
                                    value="<?php echo esc_attr($field['value']); ?>"
                                    class="regular-text microsoft-setup-input"
                                    <?php if (isset($field['placeholder'])) : ?>
                                        placeholder="<?php echo esc_attr($field['placeholder']); ?>"
                                    <?php endif; ?>
                                    data-hint="<?php printf(esc_attr__('Enter your %s.', 'administration'), $field['label']); ?>"
                                >
                                <?php if (isset($field['description'])) : ?>
                                    <p class="description">
                                        <?php echo esc_html($field['description']); ?>
                                    </p>
                                <?php endif; ?>
                                <p class="validation-error">
                                    <?php
                                    // Display field-specific errors
                                    if (!empty($field_errors)) {
                                        foreach ($field_errors as $error) {
                                            if (strpos($error, $field['label']) !== false) {
                                                echo esc_html($error);
                                            }
                                        }
                                    }
                                    ?>
                                </p>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </table>

                <?php submit_button(__('Save Settings', 'administration'), 'primary', 'administration_microsoft_submit'); ?>
            </form>

            <?php if ($credentials_saved) : ?>
                <hr>
                <h2><?php esc_html_e('Microsoft OAuth 2.0 Authorization', 'administration'); ?></h2>
                <p><?php esc_html_e('Click the button below to log in with Microsoft and authorize the application.', 'administration'); ?></p>
                <a href="<?php echo esc_url(Auth::getInstance()->getAuthorizationUrl()); ?>" class="button button-primary">
                    <?php esc_html_e('Log in with Microsoft', 'administration'); ?>
                </a>
            <?php endif; ?>
        </div>
        <?php
    }
}