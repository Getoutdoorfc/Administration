<?php

namespace Administration\Core\GeneralHandlers;

use Administration\Core\GeneralUtilities\GenneralCrypto;
use Administration\Core\Managers\LoggerManager;

/**
 * Class WpConfigHandler
 *
 * Håndterer konfigurationer for pluginet, herunder:
 * - Hentning af konfigurationsdata fra `wp-config.php`.
 * - Validering af nødvendige konfigurationer.
 * - Dynamisk tilføjelse af konfigurationskonstanter til `wp-config.php`.
 *
 * Funktionalitet:
 * - Sikrer, at krypterede værdier kan hentes og gemmes sikkert.
 * - Logger alle relevante handlinger og fejl.
 */

defined('ABSPATH') || exit;


class WpConfigHandler {

    /**
     * Henter en konfigurationsværdi fra wp-config.php.
     *
     * @param string $key Nøglen til den ønskede konfigurationsværdi.
     * @return string|null Den dekrypterede værdi, eller null hvis nøglen ikke findes.
     */
    public static function get_config($key) {
        $config = [
            'microsoft_client_id' => defined('MICROSOFT_CLIENT_ID') ? GenneralCrypto::decrypt_data(MICROSOFT_CLIENT_ID) : null,
            'microsoft_client_secret' => defined('MICROSOFT_CLIENT_SECRET') ? GenneralCrypto::decrypt_data(MICROSOFT_CLIENT_SECRET) : null,
            'microsoft_tenant_id' => defined('MICROSOFT_TENANT_ID') ? GenneralCrypto::decrypt_data(MICROSOFT_TENANT_ID) : null,
            'secret_key' => defined('PLUGIN_SECRET_KEY') ? PLUGIN_SECRET_KEY : null,
            'encryption_salt' => defined('PLUGIN_ENCRYPTION_SALT') ? PLUGIN_ENCRYPTION_SALT : null,
        ];

        if (!array_key_exists($key, $config)) {
            LoggerManager::getInstance()->error("Attempted to access undefined configuration key: {$key}");
            return null;
        }

        $value = $config[$key];
        if ($value === null) {
            LoggerManager::getInstance()->warning("Configuration value for key {$key} is null or missing.");
        } else {
            LoggerManager::getInstance()->info("Retrieved configuration value for key: {$key}");
        }

        return $value;
    }

    /**
     * Sætter en konfiguration som en konstant i wp-config.php.
     *
     * @param string $key Nøglen til konfigurationen.
     * @param string $value Den krypterede værdi.
     * @return bool True, hvis værdien blev tilføjet succesfuldt; ellers false.
     */
    public static function set_constant($key, $value) {
        if (!is_string($key) || !is_string($value)) {
            LoggerManager::getInstance()->error("Invalid key or value for setting constant: {$key}");
            return false;
        }

        $wp_config_path = ABSPATH . 'wp-config.php';

        if (!file_exists($wp_config_path)) {
            LoggerManager::getInstance()->critical("wp-config.php not found at expected path: {$wp_config_path}");
            return false;
        }

        // Krypter værdien før lagring
        $encrypted_value = GenneralCrypto::encrypt_data($value);
        if ($encrypted_value === false) {
            LoggerManager::getInstance()->error("Failed to encrypt value for key: {$key}");
            return false;
        }

        $constant_declaration = sprintf("define('%s', '%s');", $key, addslashes($encrypted_value));
        $file_contents = file_get_contents($wp_config_path);

        // Undgå duplikering af nøgler
        if (strpos($file_contents, $key) !== false) {
            LoggerManager::getInstance()->warning("Constant {$key} already defined in wp-config.php.");
            return false;
        }

        $file_contents .= PHP_EOL . $constant_declaration . PHP_EOL;

        if (file_put_contents($wp_config_path, $file_contents) !== false) {
            LoggerManager::getInstance()->info("Successfully added constant {$key} to wp-config.php.");
            return true;
        }

        LoggerManager::getInstance()->critical("Failed to write constant {$key} to wp-config.php.");
        return false;
    }

    /**
     * Validerer, at alle nødvendige konfigurationer er til stede.
     *
     * @return bool True, hvis alle nødvendige konfigurationer er valide; ellers false.
     */
    public static function validate(): bool {
        $required_keys = ['microsoft_client_id', 'microsoft_client_secret', 'microsoft_tenant_id', 'secret_key', 'encryption_salt'];

        foreach ($required_keys as $key) {
            $value = self::get_config($key);
            if (empty($value)) {
                LoggerManager::getInstance()->error("Missing required configuration: {$key}");
                return false;
            }
        }

        LoggerManager::getInstance()->info("All required configurations are valid.");
        return true;
    }

    /**
     * Logger manglende konfigurationsværdier.
     */
    public static function log_missing_config() {
        $required_keys = ['microsoft_client_id', 'microsoft_client_secret', 'microsoft_tenant_id', 'secret_key', 'encryption_salt'];

        foreach ($required_keys as $key) {
            if (empty(self::get_config($key))) {
                LoggerManager::getInstance()->error("Missing configuration value for: {$key}");
            }
        }
    }
}
