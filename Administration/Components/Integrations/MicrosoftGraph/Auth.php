<?php
namespace Administration\Components\Integrations\MicrosoftGraph;

use Administration\Components\Utilities\Crypto;
use Administration\Components\Utilities\Logger;
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

        // Log credential loading
        if (empty($this->clientId) || empty($this->clientSecret) || empty($this->tenantId)) {
            Logger::getInstance()->error('Missing Microsoft credentials: Client ID, Client Secret, or Tenant ID is empty.');
        } else {
            Logger::getInstance()->info("Microsoft credentials loaded successfully. Client ID: {$this->clientId}, Tenant ID: {$this->tenantId}");
        }

        // Set authorization and token endpoints
        $this->authorizationEndpoint = 'https://login.microsoftonline.com/' . $this->tenantId . '/oauth2/v2.0/authorize';
        $this->tokenEndpoint = 'https://login.microsoftonline.com/' . $this->tenantId . '/oauth2/v2.0/token';
        Logger::getInstance()->info("Authorization and token endpoints initialized.");
    }

    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new self();
            Logger::getInstance()->info("Auth instance created.");
        }
        return self::$instance;
    }

    public function getAuthorizationUrl() {
        // Log missing credentials
        if (empty($this->clientId) || empty($this->tenantId)) {
            Logger::getInstance()->error("Cannot generate authorization URL: Missing Client ID or Tenant ID.");
            add_settings_error('administration_microsoft_options', 'credentials_missing', __('Microsoft credentials are missing. Please configure them first.', 'administration'), 'error');
            return '';
        }

        $params = [
            'client_id' => $this->clientId,
            'response_type' => 'code',
            'redirect_uri' => $this->redirectUri,
            'response_mode' => 'query',
            'scope' => 'https://graph.microsoft.com/.default offline_access',
            'state' => wp_create_nonce('administration_microsoft_state'),
        ];

        $authUrl = $this->authorizationEndpoint . '?' . http_build_query($params);
        Logger::getInstance()->info("Generated Microsoft authorization URL: $authUrl");

        return $authUrl;
    }

    public function handleAuthorizationResponse() {
        // Log if credentials are missing
        if (empty($this->clientId) || empty($this->clientSecret) || empty($this->tenantId)) {
            Logger::getInstance()->error('Cannot handle OAuth response due to missing credentials.');
            add_settings_error('administration_microsoft_options', 'credentials_missing', __('Cannot handle OAuth response due to missing credentials.', 'administration'), 'error');
            return;
        }

        if (isset($_GET['code'], $_GET['state']) && wp_verify_nonce(sanitize_text_field($_GET['state']), 'administration_microsoft_state')) {
            $code = sanitize_text_field($_GET['code']);
            Logger::getInstance()->info("Handling OAuth response with code: $code");

            $response = wp_remote_post($this->tokenEndpoint, [
                'body' => [
                    'client_id' => $this->clientId,
                    'scope' => 'https://graph.microsoft.com/.default offline_access',
                    'code' => $code,
                    'redirect_uri' => $this->redirectUri,
                    'grant_type' => 'authorization_code',
                    'client_secret' => $this->clientSecret,
                ],
            ]);

            // Log the response and check for errors
            if (is_wp_error($response)) {
                $error_message = $response->get_error_message();
                Logger::getInstance()->error("Token request failed: $error_message");
                add_settings_error('administration_microsoft_options', 'token_error', __('Token request failed: ', 'administration') . $error_message, 'error');
                return;
            }

            $body = json_decode(wp_remote_retrieve_body($response), true);
            Logger::getInstance()->info("Token response received: " . print_r($body, true));

            if (isset($body['access_token']) && isset($body['refresh_token'])) {
                $tokenHandler = new TokenHandler();
                $tokenHandler->storeTokens($body);
                Logger::getInstance()->info("Successfully authenticated with Microsoft. Access token and refresh token stored.");

                add_settings_error('administration_microsoft_options', 'token_success', __('Successfully authenticated with Microsoft.', 'administration'), 'updated');
            } else {
                $error_description = $body['error_description'] ?? __('Unknown error.', 'administration');
                Logger::getInstance()->error("Token response invalid: $error_description");
                add_settings_error('administration_microsoft_options', 'token_error', __('Token response invalid: ', 'administration') . $error_description, 'error');
            }
        } else {
            Logger::getInstance()->error("OAuth response verification failed. Missing or invalid state.");
        }
    }
}
