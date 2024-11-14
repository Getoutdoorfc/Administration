<?php
namespace Administration\Components\Integrations\MicrosoftGraph;

use Exception;
use Administration\Utilities\Helpers;
use Administration\Utilities\Crypto;
use Administration\Utilities\Logger;
use Administration\Components\Integrations\MicrosoftGraph\TokenHandler;

defined('ABSPATH') || exit;

class Auth {
    private static $instance = null;
    private $clientId;
    private $clientSecret;
    private $tenantId;
    private $redirectUri;
    private $authorizationEndpoint;
    private $tokenEndpoint;

    private function __construct() {
        // Retrieve credentials from WordPress options
        $this->clientId = get_option('administration_microsoft_client_id', '');
        $this->clientSecret = Crypto::decrypt_data(get_option('administration_microsoft_client_secret', ''));
        $this->tenantId = get_option('administration_microsoft_tenant_id', '');
        $this->redirectUri = admin_url('admin.php?page=administration-microsoft-setup');

        // Validate credentials
        if (empty($this->clientId) || empty($this->clientSecret) || empty($this->tenantId)) {
            Logger::getInstance()->error('Missing Microsoft credentials.');
            // Optional: You might want to handle this case differently in your application
        } else {
            $this->authorizationEndpoint = 'https://login.microsoftonline.com/' . $this->tenantId . '/oauth2/v2.0/authorize';
            $this->tokenEndpoint = 'https://login.microsoftonline.com/' . $this->tenantId . '/oauth2/v2.0/token';
        }
    }

    public static function getInstance() {
        if (null === self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    public function getAuthorizationUrl() {
        if (empty($this->clientId) || empty($this->tenantId)) {
            add_settings_error('administration_microsoft_options', 'credentials_missing', __('Microsoft credentials are missing. Please configure them first.', 'administration'), 'error');
            return '';
        }

        $params = array(
            'client_id' => $this->clientId,
            'response_type' => 'code',
            'redirect_uri' => $this->redirectUri,
            'response_mode' => 'query',
            'scope' => 'https://graph.microsoft.com/.default offline_access',
            'state' => wp_create_nonce('administration_microsoft_state'),
        );

        return $this->authorizationEndpoint . '?' . http_build_query($params);
    }

    public function handleAuthorizationResponse() {
        if (empty($this->clientId) || empty($this->clientSecret) || empty($this->tenantId)) {
            Logger::getInstance()->error('Cannot handle OAuth response due to missing credentials.');
            add_settings_error('administration_microsoft_options', 'credentials_missing', __('Cannot handle OAuth response due to missing credentials.', 'administration'), 'error');
            return;
        }

        if (isset($_GET['code'], $_GET['state']) && wp_verify_nonce(sanitize_text_field($_GET['state']), 'administration_microsoft_state')) {
            $code = sanitize_text_field($_GET['code']);

            $response = wp_remote_post($this->tokenEndpoint, array(
                'body' => array(
                    'client_id' => $this->clientId,
                    'scope' => 'https://graph.microsoft.com/.default offline_access',
                    'code' => $code,
                    'redirect_uri' => $this->redirectUri,
                    'grant_type' => 'authorization_code',
                    'client_secret' => $this->clientSecret,
                ),
            ));

            if (is_wp_error($response)) {
                Logger::getInstance()->error('Token request failed.', array('error' => $response->get_error_message()));
                add_settings_error('administration_microsoft_options', 'token_error', __('Token request failed: ', 'administration') . $response->get_error_message(), 'error');
                return;
            }

            $body = json_decode(wp_remote_retrieve_body($response), true);

            if (isset($body['access_token']) && isset($body['refresh_token'])) {
                $tokenHandler = new TokenHandler();
                $tokenHandler->storeTokens($body);

                add_settings_error('administration_microsoft_options', 'token_success', __('Successfully authenticated with Microsoft.', 'administration'), 'updated');
            } else {
                $error_description = isset($body['error_description']) ? $body['error_description'] : __('Unknown error.', 'administration');
                Logger::getInstance()->error('Token response invalid.', array('response' => $body));
                add_settings_error('administration_microsoft_options', 'token_error', __('Token response invalid: ', 'administration') . $error_description, 'error');
            }
        }
    }
}