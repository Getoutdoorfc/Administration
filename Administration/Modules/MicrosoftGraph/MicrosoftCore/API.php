<?php

namespace Administration\Components\Integrations\MicrosoftGraph;

use Administration\Core\GeneralHandlers\WpConfigHandler;
use Administration\Components\Utilities\Logger;
use Administration\Modules\MicrosoftGraph\MicrosoftCore\TokenHandler;

defined('ABSPATH') || exit;

/**
 * Class API
 *
 * Håndterer HTTP-anmodninger til Microsoft Graph API.
 */
class API {

    private string $base_url = 'https://graph.microsoft.com/v1.0';
    private TokenHandler $token_handler;
    private int $timeout = 15;
    private int $retry_attempts = 3;

    public function __construct() {
        $this->token_handler = new TokenHandler();
    }

    /**
     * Sender en POST-anmodning til det angivne endpoint.
     *
     * @param string $endpoint API-endpoint.
     * @param array  $body     Anmodningsbody.
     * @return array|false Respons array eller false ved fejl.
     */
    public function post(string $endpoint, array $body = []) {
        return $this->request('POST', $endpoint, $body);
    }

    /**
     * Sender en GET-anmodning til det angivne endpoint.
     *
     * @param string $endpoint API-endpoint.
     * @return array|false Respons array eller false ved fejl.
     */
    public function get(string $endpoint) {
        return $this->request('GET', $endpoint);
    }

    /**
     * Sender en DELETE-anmodning til det angivne endpoint.
     *
     * @param string $endpoint API-endpoint.
     * @return array|false Respons array eller false ved fejl.
     */
    public function delete(string $endpoint) {
        return $this->request('DELETE', $endpoint);
    }

    /**
     * Håndterer API-anmodninger med forskellige metoder.
     *
     * @param string $method   HTTP-metode (GET, POST, DELETE, etc.).
     * @param string $endpoint API-endpoint.
     * @param array  $body     Anmodningsbody (kun for POST/PUT).
     * @return array|false Respons array eller false ved fejl.
     */
    private function request(string $method, string $endpoint, array $body = []) {
        Logger::getInstance()->info("Starting {$method} request to {$endpoint}...");

        // Valider konfigurationen før anmodning
        if (!$this->validate_configuration()) {
            Logger::getInstance()->error('Microsoft Graph configuration is invalid. Request aborted.');
            return false;
        }

        $access_token = $this->token_handler->get_access_token();
        if (!$access_token) {
            Logger::getInstance()->error('Failed to retrieve access token. Request aborted.');
            return false;
        }

        $args = [
            'headers' => [
                'Authorization' => 'Bearer ' . $access_token,
                'Content-Type' => 'application/json',
            ],
            'method' => $method,
            'timeout' => $this->timeout,
        ];

        if (!empty($body)) {
            $args['body'] = wp_json_encode($body);
            if (json_last_error() !== JSON_ERROR_NONE) {
                Logger::getInstance()->error('Invalid JSON encoding for request body.', ['body' => $body]);
                return false;
            }
        }

        for ($attempt = 1; $attempt <= $this->retry_attempts; $attempt++) {
            $response = wp_remote_request($this->base_url . $endpoint, $args);

            if (is_wp_error($response)) {
                Logger::getInstance()->warning("Attempt {$attempt} for {$method} request to {$endpoint} failed.", [
                    'error' => $response->get_error_message(),
                ]);

                if ($attempt < $this->retry_attempts) {
                    usleep(500000); // 0,5 sekunders forsinkelse
                    continue;
                } else {
                    Logger::getInstance()->error("All retry attempts for {$method} request to {$endpoint} failed.");
                    return false;
                }
            }

            $status_code = wp_remote_retrieve_response_code($response);
            if ($status_code < 200 || $status_code >= 300) {
                Logger::getInstance()->warning("HTTP {$status_code} received for {$method} request to {$endpoint}.", [
                    'response_body' => wp_remote_retrieve_body($response),
                ]);

                if ($attempt < $this->retry_attempts) {
                    usleep(500000);
                    continue;
                } else {
                    Logger::getInstance()->error("Failed after {$this->retry_attempts} attempts for {$method} request.");
                    return false;
                }
            }

            $response_body = wp_remote_retrieve_body($response);
            $decoded_body = json_decode($response_body, true);

            if (json_last_error() !== JSON_ERROR_NONE) {
                Logger::getInstance()->error('Invalid JSON response.', ['response_body' => $response_body]);
                return false;
            }

            Logger::getInstance()->info("Request succeeded.", ['response' => $decoded_body]);
            return $decoded_body;
        }

        return false;
    }

    /**
     * Validerer, at konfigurationen er korrekt.
     *
     * @return bool True hvis valid; ellers false.
     */
    private function validate_configuration(): bool {
        if (!WpConfigHandler::validate()) {
            Logger::getInstance()->error('Configuration validation failed.');
            return false;
        }
        return true;
    }
}
