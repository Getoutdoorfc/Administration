<?php
namespace Administration\Components\Integrations\MicrosoftGraph;

use Administration\Components\Utilities\Logger;
use Administration\Components\Utilities\ConfigHandler;

defined('ABSPATH') || exit;

/**
 * Class Auth
 *
 * Håndterer OAuth2-autentificering med Microsoft.
 *
 * @package Administration\Components\Integrations\MicrosoftGraph
 */
class Auth {

    private static $instance = null;

    private $client_id;
    private $client_secret;
    private $tenant_id;
    private $redirect_uri;
    private $authorization_endpoint;
    private $token_endpoint;

    private function __construct() {
        Logger::getInstance()->info('Initializing Auth class...');

        // Hent credentials fra ConfigHandler
        $this->client_id = ConfigHandler::get_config('client_id');
        $this->client_secret = ConfigHandler::get_config('client_secret');
        $this->tenant_id = ConfigHandler::get_config('tenant_id');
        $this->redirect_uri = admin_url('admin.php?page=administration-microsoft-setup');

        $this->authorization_endpoint = $this->build_endpoint('authorize');
        $this->token_endpoint = $this->build_endpoint('token');

        $this->validate_credentials();

        Logger::getInstance()->info('Auth class initialized.', [
            'client_id_status' => !empty($this->client_id) ? 'SET' : 'NOT SET',
            'tenant_id_status' => !empty($this->tenant_id) ? 'SET' : 'NOT SET',
        ]);
    }

    public static function get_instance(): self {
        if (self::$instance === null) {
            self::$instance = new Auth();
        }
        return self::$instance;
    }

    public function get_authorization_url(): string {
        Logger::getInstance()->info('Generating authorization URL...');

        if (empty($this->client_id) || empty($this->tenant_id) || empty($this->authorization_endpoint)) {
            Logger::getInstance()->error('Authorization URL cannot be generated: Missing credentials or endpoint.');
            return __('Authorization URL cannot be generated due to missing configuration. Please check your Microsoft settings.', 'administration');
        }

        $params = [
            'client_id' => $this->client_id,
            'response_type' => 'code',
            'redirect_uri' => $this->redirect_uri,
            'response_mode' => 'query',
            'scope' => 'https://graph.microsoft.com/.default offline_access',
            'state' => wp_create_nonce('administration_microsoft_state'),
        ];

        $url = $this->authorization_endpoint . '?' . http_build_query($params);
        Logger::getInstance()->info('Authorization URL generated successfully.', ['url' => $url]);
        return $url;
    }

    public function handle_authorization_response(): void {
        Logger::getInstance()->info('Handling authorization response...');

        if (!isset($_GET['code']) || !isset($_GET['state'])) {
            $this->add_error('Authorization response is missing required parameters.');
            return;
        }

        if (!wp_verify_nonce(sanitize_text_field($_GET['state']), 'administration_microsoft_state')) {
            $this->add_error('State verification failed.');
            return;
        }

        $code = sanitize_text_field($_GET['code']);
        Logger::getInstance()->info('Authorization code received.', ['code' => 'REDACTED']);

        if (!$this->token_endpoint) {
            $this->add_error('Token endpoint is not set. Check Tenant ID configuration.');
            return;
        }

        $response = wp_remote_post($this->token_endpoint, [
            'body' => [
                'client_id' => $this->client_id,
                'scope' => 'https://graph.microsoft.com/.default offline_access',
                'code' => $code,
                'redirect_uri' => $this->redirect_uri,
                'grant_type' => 'authorization_code',
                'client_secret' => $this->client_secret,
            ],
            'timeout' => 15,
        ]);

        $this->process_token_response($response);
    }

    private function process_token_response($response): void {
        if (is_wp_error($response)) {
            $error_message = $response->get_error_message();
            Logger::getInstance()->error('Token request failed: ' . $error_message);
            $this->add_error('Token request failed: ' . $error_message);
            return;
        }

        $body = json_decode(wp_remote_retrieve_body($response), true);

        if (isset($body['access_token'], $body['refresh_token'])) {
            Logger::getInstance()->info('Tokens received successfully.', [
                'access_token' => 'REDACTED',
                'refresh_token' => 'REDACTED',
                'expires_in' => $body['expires_in'] ?? 'UNKNOWN',
            ]);

            $token_handler = new TokenHandler();
            $token_handler->store_tokens($body);

            add_settings_error('administration_microsoft_options', 'token_success', __('Successfully authenticated with Microsoft.', 'administration'), 'updated');
        } else {
            Logger::getInstance()->error('Token response invalid.', $body);
            $this->add_error('Invalid token response.');
        }
    }

    private function validate_credentials(): void {
        if (empty($this->client_id)) {
            Logger::getInstance()->error('Client ID is missing.');
        }
        if (empty($this->tenant_id)) {
            Logger::getInstance()->error('Tenant ID is missing.');
        }
    }

    private function build_endpoint(string $type): ?string {
        if (empty($this->tenant_id)) {
            Logger::getInstance()->error("Cannot build {$type} endpoint: Tenant ID is missing.");
            return null;
        }

        return 'https://login.microsoftonline.com/' . $this->tenant_id . '/oauth2/v2.0/' . $type;
    }

    private function add_error(string $message): void {
        Logger::getInstance()->error($message);
        add_settings_error('administration_microsoft_options', 'auth_error', __($message, 'administration'), 'error');
    }
}
