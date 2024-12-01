<?php

namespace Administration\Modules\MicrosoftGraph\MicrosoftCore;

use Administration\Components\Utilities\ConfigHandler;
use Administration\Components\Utilities\Crypto;
use Administration\Components\Utilities\Logger;

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Class TokenHandler
 *
 * Håndterer Microsoft Graph API tokens ved at gemme, hente, kryptere og forny dem.
 *
 * Metoder:
 * - store_tokens(array $tokens): bool
 *   Gemmer krypterede tokens i databasen og logger handlingen.
 *
 * - get_access_token(): string|false
 *   Henter access token fra databasen. Fornyer det automatisk, hvis det er udløbet.
 *
 * - refresh_access_token(): bool
 *   Fornyer tokens ved hjælp af refresh token og opdaterer dem i databasen.
 *
 * - is_token_expired(int $expires): bool
 *   Kontrollerer om et token er udløbet baseret på dets udløbstidspunkt.
 *
 * Afhængigheder:
 * - Crypto: Bruges til kryptering og dekryptering af tokens.
 * - ConfigHandler: Henter nødvendige konfigurationsdata som Client ID og Client Secret.
 * - Logger: Logger alle relevante handlinger og fejl.
 */
class TokenHandler {

    /**
     * Gemmer krypterede tokens i databasen.
     *
     * @param array $tokens Array indeholdende:
     *   - 'access_token' (string): Token til API-anmodninger.
     *   - 'refresh_token' (string): Token til fornyelse af adgangstoken.
     *   - 'expires_in' (int): Antal sekunder indtil udløb.
     *
     * @return bool True ved succes, ellers false.
     */
    public function store_tokens(array $tokens): bool {
        if (!isset($tokens['access_token'], $tokens['refresh_token'], $tokens['expires_in'])) {
            Logger::getInstance()->error('Invalid token data provided for storage.');
            return false;
        }

        try {
            // Krypter tokens
            $encrypted_access_token = Crypto::encrypt_data($tokens['access_token']);
            $encrypted_refresh_token = Crypto::encrypt_data($tokens['refresh_token']);

            // Gem tokens og deres udløbstidspunkt
            update_option('administration_microsoft_access_token', $encrypted_access_token);
            update_option('administration_microsoft_refresh_token', $encrypted_refresh_token);
            update_option('administration_microsoft_token_expires', time() + $tokens['expires_in']);

            Logger::getInstance()->info('Tokens stored successfully.', [
                'expires_in' => $tokens['expires_in'],
            ]);
            return true;
        } catch (\Exception $e) {
            Logger::getInstance()->error('Failed to store tokens: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Henter access token, fornyer det hvis nødvendigt.
     *
     * @return string|false Access token som en dekrypteret streng, eller false ved fejl.
     */
    public function get_access_token() {
        Logger::getInstance()->info('Fetching access token...');

        $access_token = get_option('administration_microsoft_access_token', '');
        $expires = get_option('administration_microsoft_token_expires', 0);

        if ($this->is_token_expired($expires)) {
            Logger::getInstance()->info('Access token expired. Attempting to refresh...');
            if (!$this->refresh_access_token()) {
                Logger::getInstance()->error('Failed to refresh access token.');
                return false;
            }
            $access_token = get_option('administration_microsoft_access_token', '');
        }

        if (empty($access_token)) {
            Logger::getInstance()->error('Access token is not available.');
            return false;
        }

        // Dekrypter access token
        $decrypted_token = Crypto::decrypt_data($access_token);
        if (!$decrypted_token) {
            Logger::getInstance()->error('Decryption of access token failed.');
            return false;
        }

        return $decrypted_token;
    }

    /**
     * Fornyer access token ved hjælp af refresh token.
     *
     * @return bool True ved succes, ellers false.
     */
    private function refresh_access_token(): bool {
        $refresh_token = get_option('administration_microsoft_refresh_token', '');

        if (empty($refresh_token)) {
            Logger::getInstance()->error('Refresh token not available.');
            return false;
        }

        // Dekrypter refresh token
        $refresh_token = Crypto::decrypt_data($refresh_token);
        if (!$refresh_token) {
            Logger::getInstance()->error('Decryption of refresh token failed.');
            return false;
        }

        $token_endpoint = 'https://login.microsoftonline.com/' . ConfigHandler::get_config('microsoft_tenant_id') . '/oauth2/v2.0/token';

        Logger::getInstance()->info('Sending request to refresh access token.');

        $response = wp_remote_post($token_endpoint, [
            'body' => [
                'client_id' => ConfigHandler::get_config('microsoft_client_id'),
                'client_secret' => ConfigHandler::get_config('microsoft_client_secret'),
                'grant_type' => 'refresh_token',
                'refresh_token' => $refresh_token,
                'scope' => 'https://graph.microsoft.com/.default offline_access',
            ],
            'timeout' => 15,
        ]);

        if (is_wp_error($response)) {
            $error_message = $response->get_error_message();
            Logger::getInstance()->error('Token refresh failed: ' . $error_message);
            return false;
        }

        $body = json_decode(wp_remote_retrieve_body($response), true);

        if (isset($body['access_token'], $body['refresh_token'])) {
            Logger::getInstance()->info('Tokens refreshed successfully.', [
                'expires_in' => $body['expires_in'] ?? 'unknown',
            ]);
            return $this->store_tokens($body);
        } else {
            Logger::getInstance()->error('Invalid response during token refresh.', [
                'response_body' => $body,
            ]);
            return false;
        }
    }

    /**
     * Tjekker om et token er udløbet.
     *
     * @param int $expires Timestamp for udløb.
     * @return bool True hvis token er udløbet, ellers false.
     */
    private function is_token_expired(int $expires): bool {
        $is_expired = time() >= $expires;
        Logger::getInstance()->info('Checking if token is expired.', [
            'expires_at' => $expires,
            'current_time' => time(),
            'is_expired' => $is_expired ? 'Yes' : 'No',
        ]);
        return $is_expired;
    }
}
