<?php
namespace Administration\Components\Utilities;

defined('ABSPATH') || exit;

class Crypto {
    private static $encryption_key = MS_CRYPT_KEY; // Define MS_CRYPT_KEY in wp-config.php
    private static $iv_length = 16; // For AES-256-CBC, the IV length is 16 bytes

    public static function encrypt_data($data) {
        $iv = openssl_random_pseudo_bytes(self::$iv_length);
        $encrypted = openssl_encrypt($data, 'AES-256-CBC', self::$encryption_key, 0, $iv);
        return base64_encode($iv . $encrypted);
    }

    public static function decrypt_data($data) {
        $data = base64_decode($data);
        $iv = substr($data, 0, self::$iv_length);
        $encrypted = substr($data, self::$iv_length);
        return openssl_decrypt($encrypted, 'AES-256-CBC', self::$encryption_key, 0, $iv);
    }
}