<?php

namespace Administration\Core\GeneralUtilities;

use Administration\Core\Managers\LoggerManager;
use Administration\Core\GeneralHandlers\WpConfigHandler;

defined('ABSPATH') || exit;

/**
 * Class Crypto
 *
 * En utility-klasse til håndtering af kryptering og dekryptering af følsomme data.
 * Denne klasse benytter AES-256-CBC-algoritmen for at sikre data med en stærk krypteringsnøgle.
 *
 * Primære anvendelser:
 * - Kryptering og dekryptering af Microsoft API tokens (access tokens og refresh tokens).
 * - Kryptering og dekryptering af Microsoft credentials (Client ID, Client Secret, Tenant ID).
 *
 * Funktionalitet:
 * 1. Genererer en sikker krypteringsnøgle baseret på konfiguration og WordPress salt.
 * 2. Understøtter fejlhåndtering og logging for alle operationer.
 *
 * Anbefaling:
 * Denne klasse er designet til generisk brug og kan genbruges til andre krypteringsbehov uden at være låst til Microsoft-integration.
 */
class GenneralCrypto {

    /**
     * Genererer en sikker krypteringsnøgle baseret på konfigurationsindstillinger.
     *
     * Krypteringsnøglen kombinerer:
     * - En hemmelig nøgle defineret i wp-config.php.
     * - En plugin-specifik salt.
     * - WordPress' `wp_salt()` for ekstra sikkerhed.
     *
     * @return string Krypteringsnøgle.
     * @throws \Exception Hvis krypteringsnøglen ikke kan genereres.
     */
    private static function get_encryption_key(): string {
        LoggerManager::getInstance()->info('Fetching encryption key...');

        $secret_key = WpConfigHandler::get_config('secret_key');
        $salt = WpConfigHandler::get_config('encryption_salt');

        if (empty($secret_key) || empty($salt)) {
            LoggerManager::getInstance()->error('Encryption key or salt is missing in configuration.');
            throw new \Exception('Encryption key or salt is not properly configured.');
        }

        // Kombiner WordPress' salt, secret_key og plugin-salt
        $key = hash('sha256', wp_salt('auth') . $secret_key . $salt, true);

        if (!$key) {
            LoggerManager::getInstance()->critical('Failed to generate encryption key.');
            throw new \Exception('Encryption key generation failed.');
        }

        LoggerManager::getInstance()->info('Encryption key successfully generated.');
        return $key;
    }

    /**
     * Krypterer data ved hjælp af AES-256-CBC.
     *
     * @param string $data Data, der skal krypteres.
     * @return string|false Krypteret data som Base64-enkodet streng, eller false ved fejl.
     */
    public static function encrypt_data(string $data) {
        if (empty($data)) {
            LoggerManager::getInstance()->error('Encryption failed: Input data is empty.');
            return false;
        }

        LoggerManager::getInstance()->info('Encrypting data...');
        try {
            $encryption_key = self::get_encryption_key();
            $iv_length = openssl_cipher_iv_length('aes-256-cbc');
            $iv = openssl_random_pseudo_bytes($iv_length);

            if ($iv === false) {
                throw new \Exception('Failed to generate initialization vector.');
            }

            $encrypted = openssl_encrypt($data, 'aes-256-cbc', $encryption_key, 0, $iv);

            if ($encrypted === false) {
                throw new \Exception('Encryption process failed.');
            }

            $result = base64_encode($iv . $encrypted);
            LoggerManager::getInstance()->info('Data successfully encrypted.');
            return $result;
        } catch (\Exception $e) {
            LoggerManager::getInstance()->critical('Encryption failed: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Dekrypterer data ved hjælp af AES-256-CBC.
     *
     * @param string $data Data, der skal dekrypteres (Base64-enkodet streng).
     * @return string|false Dekrypteret data som klar tekst, eller false ved fejl.
     */
    public static function decrypt_data(string $data) {
        if (empty($data)) {
            LoggerManager::getInstance()->error('Decryption failed: Input data is empty.');
            return false;
        }

        LoggerManager::getInstance()->info('Decrypting data...');
        try {
            $encryption_key = self::get_encryption_key();
            $decoded_data = base64_decode($data, true);

            if ($decoded_data === false) {
                throw new \Exception('Base64 decoding failed.');
            }

            $iv_length = openssl_cipher_iv_length('aes-256-cbc');
            $iv = substr($decoded_data, 0, $iv_length);
            $encrypted = substr($decoded_data, $iv_length);

            if (empty($iv) || empty($encrypted)) {
                throw new \Exception('Incomplete encrypted data.');
            }

            $decrypted = openssl_decrypt($encrypted, 'aes-256-cbc', $encryption_key, 0, $iv);

            if ($decrypted === false) {
                throw new \Exception('Decryption process failed.');
            }

            LoggerManager::getInstance()->info('Data successfully decrypted.');
            return $decrypted;
        } catch (\Exception $e) {
            LoggerManager::getInstance()->critical('Decryption failed: ' . $e->getMessage());
            return false;
        }
    }
}
