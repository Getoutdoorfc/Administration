<?php
namespace Administration\Components\Integrations\MicrosoftGraph;

use Administration\Utilities\Helpers;
use Administration\Utilities\Logger;

defined('ABSPATH') || exit;

/**
 * Class TokenHandler
 *
 * Håndterer opbevaring og fornyelse af tokens til Microsoft Graph API.
 *
 * @package Administration\Components\Integrations\MicrosoftGraph
 */
class TokenHandler {

    /**
     * Encryption key.
     *
     * @var string
     */
    private $encryptionKey;

    /**
     * Constructor.
     * Initialiserer TokenHandler-klassen og sikrer, at krypteringsnøglen er sat op korrekt.
     */
    public function __construct() {
        $this->encryptionKey = defined('ADMINISTRATION_ENCRYPTION_KEY') ? ADMINISTRATION_ENCRYPTION_KEY : '';

        if (empty($this->encryptionKey)) {
            $this->encryptionKey = base64_encode(openssl_random_pseudo_bytes(32));
            // Gem krypteringsnøglen i wp-config.php
            $this->addEncryptionKeyToConfig($this->encryptionKey);
        }
    }

    /**
     * Tilføjer krypteringsnøglen til wp-config.php, hvis den ikke allerede er der.
     *
     * @param string $key
     */
    private function addEncryptionKeyToConfig($key) {
        $config_file = ABSPATH . 'wp-config.php';

        if (is_writable($config_file)) {
            $config_contents = file_get_contents($config_file);

            if (!defined('ADMINISTRATION_ENCRYPTION_KEY')) {
                $config_contents = preg_replace(
                    "/(\/\*\* That's all, stop editing! Happy publishing\. \*\/)/",
                    "define('ADMINISTRATION_ENCRYPTION_KEY', '{$key}');\n$1",
                    $config_contents
                );
                file_put_contents($config_file, $config_contents);
            }
        } else {
            wp_die(__('Kan ikke skrive til wp-config.php. Sørg for, at filen er skrivbar.', 'administration'));
        }
    }

    /**
     * Krypterer og gemmer tokens sikkert.
     *
     * @param array $tokens
     */
    public function storeTokens($tokens) {
        $encryptedAccessToken  = $this->encrypt($tokens['access_token']);
        $encryptedRefreshToken = $this->encrypt($tokens['refresh_token']);

        update_option('administration_microsoft_access_token', $encryptedAccessToken);
        update_option('administration_microsoft_refresh_token', $encryptedRefreshToken);
        update_option('administration_microsoft_token_expires', time() + $tokens['expires_in']);
    }

    /**
     * Henter adgangstoken, fornyer den hvis nødvendigt.
     *
     * @return string|false Adgangstoken eller false ved fejl.
     */
    public function getAccessToken() {
        $accessToken = get_option('administration_microsoft_access_token');
        $expires     = get_option('administration_microsoft_token_expires');

        if (empty($accessToken) || time() >= $expires) {
            // Forsøger at forny token
            if (!$this->refreshAccessToken()) {
                return false;
            }
            $accessToken = get_option('administration_microsoft_access_token');
        }

        return $this->decrypt($accessToken);
    }

    /**
     * Fornyer adgangstoken ved hjælp af fornyelsestoken.
     *
     * @return bool True ved succes, false ved fejl.
     */
    public function refreshAccessToken() {
        $refreshToken = get_option('administration_microsoft_refresh_token');
        if (empty($refreshToken)) {
            return false;
        }
        $refreshToken = $this->decrypt($refreshToken);

        $auth = Auth::get_instance();

        $response = wp_remote_post($auth->tokenEndpoint, array(
            'body' => array(
                'client_id'     => $auth->clientId,
                'client_secret' => $auth->clientSecret,
                'grant_type'    => 'refresh_token',
                'refresh_token' => $refreshToken,
                'scope'         => 'https://graph.microsoft.com/.default offline_access',
            ),
        ));

        if (is_wp_error($response)) {
            Logger::get_instance()->error('Token refresh failed.', array('error' => $response->get_error_message()));
            return false;
        }

        $body = json_decode(wp_remote_retrieve_body($response), true);

        if (isset($body['access_token']) && isset($body['refresh_token'])) {
            $this->storeTokens($body);
            return true;
        } else {
            Logger::get_instance()->error('Token refresh response invalid.', array('response' => $body));
            return false;
        }
    }

    /**
     * Krypterer en streng.
     *
     * @param string $data
     * @return string Krypteret data
     */
    private function encrypt($data) {
        $encryptionKey = base64_decode($this->encryptionKey);
        $iv            = openssl_random_pseudo_bytes(openssl_cipher_iv_length('aes-256-cbc'));
        $encrypted     = openssl_encrypt($data, 'aes-256-cbc', $encryptionKey, 0, $iv);
        return base64_encode($encrypted . '::' . $iv);
    }

    /**
     * Dekrypterer en streng.
     *
     * @param string $data
     * @return string Dekrypteret data
     */
    private function decrypt($data) {
        $encryptionKey = base64_decode($this->encryptionKey);
        list($encrypted_data, $iv) = explode('::', base64_decode($data), 2);
        return openssl_decrypt($encrypted_data, 'aes-256-cbc', $encryptionKey, 0, $iv);
    }
}
