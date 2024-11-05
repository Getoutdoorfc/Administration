<?php
if (!defined('ABSPATH')) {
    exit; // Stop direkte adgang
}

class Administration_MSGraph_Auth {
    private $client_id;
    private $client_secret;
    private $tenant_id;
    private $redirect_uri;

    public function __construct() {
        $this->client_id = get_option('administration_client_id');
        $this->client_secret = get_option('administration_client_secret');
        $this->tenant_id = get_option('administration_tenant_id');
        $this->redirect_uri = admin_url('admin.php?page=msgraph_auth_callback');
    }

    public function get_authorization_url() {
        $url = "https://login.microsoftonline.com/{$this->tenant_id}/oauth2/v2.0/authorize";
        $params = [
            'client_id' => $this->client_id,
            'response_type' => 'code',
            'redirect_uri' => $this->redirect_uri,
            'response_mode' => 'query',
            'scope' => 'openid profile offline_access User.Read',
            'state' => wp_create_nonce('msgraph_auth')
        ];
        return $url . '?' . http_build_query($params);
    }

    public function handle_callback() {
        if (!isset($_GET['code']) || !wp_verify_nonce($_GET['state'], 'msgraph_auth')) {
            return;
        }

        $code = sanitize_text_field($_GET['code']);
        $token = $this->get_token($code);

        if ($token) {
            set_transient('administration_access_token', $this->encrypt_data($token['access_token']), $token['expires_in']);
            set_transient('administration_refresh_token', $this->encrypt_data($token['refresh_token']), 30 * DAY_IN_SECONDS);
            set_transient('administration_token_expires', time() + $token['expires_in'], $token['expires_in']);
        }
    }

    private function get_token($code) {
        $url = "https://login.microsoftonline.com/{$this->tenant_id}/oauth2/v2.0/token";
        $response = wp_remote_post($url, [
            'body' => [
                'client_id' => $this->client_id,
                'client_secret' => $this->client_secret,
                'code' => $code,
                'redirect_uri' => $this->redirect_uri,
                'grant_type' => 'authorization_code'
            ]
        ]);

        if (is_wp_error($response)) {
            error_log('Error fetching token: ' . $response->get_error_message());
            return false;
        }

        $body = wp_remote_retrieve_body($response);
        return json_decode($body, true);
    }

    public function get_access_token() {
        $access_token = $this->decrypt_data(get_transient('administration_access_token'));
        $token_expires = get_transient('administration_token_expires');

        // Check for cached token with a 5-minute buffer
        if ($access_token && $token_expires && (time() + 300) < $token_expires) {
            return $access_token;
        }

        return $this->refresh_access_token();
    }

    private function refresh_access_token() {
        $refresh_token = $this->decrypt_data(get_transient('administration_refresh_token'));
        $url = "https://login.microsoftonline.com/{$this->tenant_id}/oauth2/v2.0/token";
        $response = wp_remote_post($url, [
            'body' => [
                'client_id' => $this->client_id,
                'client_secret' => $this->client_secret,
                'refresh_token' => $refresh_token,
                'grant_type' => 'refresh_token'
            ]
        ]);

        if (is_wp_error($response)) {
            error_log('Error refreshing token: ' . $response->get_error_message());
            return false;
        }

        $body = wp_remote_retrieve_body($response);
        $token = json_decode($body, true);

        if ($token) {
            set_transient('administration_access_token', $this->encrypt_data($token['access_token']), $token['expires_in']);
            set_transient('administration_refresh_token', $this->encrypt_data($token['refresh_token']), 30 * DAY_IN_SECONDS);
            set_transient('administration_token_expires', time() + $token['expires_in'], $token['expires_in']);
            return $token['access_token'];
        }

        return false;
    }

    private function encrypt_data($data) {
        $encryption_key = base64_decode(SECRET_KEY);
        $iv = openssl_random_pseudo_bytes(openssl_cipher_iv_length('aes-256-cbc'));
        $encrypted = openssl_encrypt($data, 'aes-256-cbc', $encryption_key, 0, $iv);
        return base64_encode($encrypted . '::' . $iv);
    }

    private function decrypt_data($data) {
        $encryption_key = base64_decode(SECRET_KEY);
        list($encrypted_data, $iv) = explode('::', base64_decode($data), 2);
        return openssl_decrypt($encrypted_data, 'aes-256-cbc', $encryption_key, 0, $iv);
    }
}