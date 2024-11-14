<?php
namespace Administration\Components\Integrations\MicrosoftGraph;

use Exception;
use Administration\Utilities\Logger;

defined( 'ABSPATH' ) || exit;

/**
 * Class API
 *
 * Håndterer HTTP-anmodninger til Microsoft Graph API.
 *
 * @package Administration\Components\Integrations\MicrosoftGraph
 */
class API {

    /**
     * Base URL for Microsoft Graph API.
     *
     * @var string
     */
    private $baseUrl;

    /**
     * Token handler instance.
     *
     * @var TokenHandler
     */
    private $tokenHandler;

    /**
     * Constructor.
     *
     * @param string       $baseUrl
     * @param TokenHandler $tokenHandler
     */
    public function __construct( $baseUrl, $tokenHandler ) {
        $this->baseUrl      = $baseUrl;
        $this->tokenHandler = $tokenHandler;
    }

    /**
     * Sends a GET request to the specified endpoint.
     *
     * @param string $endpoint
     * @return array|false
     */
    public function get( $endpoint ) {
        try {
            $cacheKey       = 'administration_api_cache_' . md5( $endpoint );
            $cachedResponse = get_transient( $cacheKey );

            if ( $cachedResponse ) {
                return $cachedResponse;
            }

            $accessToken = $this->tokenHandler->getAccessToken();
            if ( ! $accessToken ) {
                throw new \Exception( 'Access token not available.' );
            }

            $response = wp_remote_get( $this->baseUrl . $endpoint, array(
                'headers' => array(
                    'Authorization' => 'Bearer ' . $accessToken,
                    'Content-Type'  => 'application/json',
                ),
            ) );

            if ( is_wp_error( $response ) ) {
                Logger::getInstance()->error(
                    'GET request error',
                    array(
                        'error'    => $response->get_error_message(),
                        'endpoint' => $endpoint,
                    )
                );
                return false;
            }

            $statusCode = wp_remote_retrieve_response_code( $response );
            if ( $statusCode !== 200 ) {
                throw new \Exception( 'API request failed with status code ' . $statusCode );
            }

            $data = json_decode( wp_remote_retrieve_body( $response ), true );

            // Cache response for 15 minutes
            set_transient( $cacheKey, $data, 15 * MINUTE_IN_SECONDS );

            return $data;
        } catch ( \Exception $e ) {
            Logger::getInstance()->error( 'GET request error: ' . $e->getMessage(), array( 'endpoint' => $endpoint ) );
            return false;
        }
    }

    /**
     * Sends a POST request to the specified endpoint.
     *
     * @param string $endpoint
     * @param array  $body
     * @return array|false
     */
    public function post( $endpoint, $body ) {
        $accessToken = $this->tokenHandler->getAccessToken();
        if ( ! $accessToken ) {
            return false;
        }

        $response = wp_remote_post( $this->baseUrl . $endpoint, array(
            'headers' => array(
                'Authorization' => 'Bearer ' . $accessToken,
                'Content-Type'  => 'application/json',
            ),
            'body' => wp_json_encode( $body ),
        ) );

        if ( is_wp_error( $response ) ) {
            Logger::getInstance()->error( 'Microsoft Graph API POST request failed.', array( 'error' => $response->get_error_message() ) );
            return false;
        }

        $statusCode = wp_remote_retrieve_response_code( $response );
        if ( $statusCode < 200 || $statusCode >= 300 ) {
            Logger::getInstance()->error( 'Microsoft Graph API POST request failed with status code ' . $statusCode, array( 'response' => wp_remote_retrieve_body( $response ) ) );
            return false;
        }

        return json_decode( wp_remote_retrieve_body( $response ), true );
    }

    /**
     * Sends a PATCH request to the specified endpoint.
     *
     * @param string $endpoint
     * @param array  $body
     * @return array|false
     */
    public function patch( $endpoint, $body ) {
        $accessToken = $this->tokenHandler->getAccessToken();
        if ( ! $accessToken ) {
            return false;
        }

        $response = wp_remote_request( $this->baseUrl . $endpoint, array(
            'method'  => 'PATCH',
            'headers' => array(
                'Authorization' => 'Bearer ' . $accessToken,
                'Content-Type'  => 'application/json',
            ),
            'body' => wp_json_encode( $body ),
        ) );

        if ( is_wp_error( $response ) ) {
            Logger::getInstance()->error( 'Microsoft Graph API PATCH request failed.', array( 'error' => $response->get_error_message() ) );
            return false;
        }

        $statusCode = wp_remote_retrieve_response_code( $response );
        if ( $statusCode < 200 || $statusCode >= 300 ) {
            Logger::getInstance()->error( 'Microsoft Graph API PATCH request failed with status code ' . $statusCode, array( 'response' => wp_remote_retrieve_body( $response ) ) );
            return false;
        }

        return json_decode( wp_remote_retrieve_body( $response ), true );
    }

    /**
     * Sends a DELETE request to the specified endpoint.
     *
     * @param string $endpoint
     * @return bool
     */
    public function delete( $endpoint ) {
        $accessToken = $this->tokenHandler->getAccessToken();
        if ( ! $accessToken ) {
            return false;
        }

        $response = wp_remote_request( $this->baseUrl . $endpoint, array(
            'method'  => 'DELETE',
            'headers' => array(
                'Authorization' => 'Bearer ' . $accessToken,
                'Content-Type'  => 'application/json',
            ),
        ) );

        if ( is_wp_error( $response ) ) {
            Logger::getInstance()->error( 'Microsoft Graph API DELETE request failed.', array( 'error' => $response->get_error_message() ) );
            return false;
        }

        $statusCode = wp_remote_retrieve_response_code( $response );
        if ( $statusCode < 200 || $statusCode >= 300 ) {
            Logger::getInstance()->error( 'Microsoft Graph API DELETE request failed with status code ' . $statusCode, array( 'response' => wp_remote_retrieve_body( $response ) ) );
            return false;
        }

        return true;
    }
}
