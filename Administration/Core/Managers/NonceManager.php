<?php

namespace Administration\Core\Managers;

defined('ABSPATH') || exit;

/**
 * Class NonceManager
 *
 * Håndterer oprettelse og verifikation af nonces i WordPress.
 *
 * @package Administration\Core\Managers
 */
class NonceManager
{
    /**
     * Opretter en nonce.
     *
     * @param string $action Handlingen, som noncen er knyttet til.
     * @return string Den genererede nonce.
     */
    public static function create_nonce(string $action): string
    {
        return wp_create_nonce($action);
    }

    /**
     * Verificerer en nonce.
     *
     * @param string $nonce Noncen, der skal verificeres.
     * @param string $action Handlingen, som noncen er knyttet til.
     * @return bool True, hvis noncen er gyldig, ellers false.
     */
    public static function verify_nonce(string $nonce, string $action): bool
    {
        return wp_verify_nonce($nonce, $action) !== false;
    }

    /**
     * Tilføjer en nonce til en URL.
     *
     * @param string $url URL'en, som noncen skal tilføjes til.
     * @param string $action Handlingen, som noncen er knyttet til.
     * @return string URL'en med den tilføjede nonce.
     */
    public static function add_nonce_to_url(string $url, string $action): string
    {
        return wp_nonce_url($url, $action);
    }

    /**
     * Tilføjer en nonce til en formular.
     *
     * @param string $action Handlingen, som noncen er knyttet til.
     * @return string HTML-koden for noncen.
     */
    public static function add_nonce_to_form(string $action): string
    {
        return wp_nonce_field($action, '_wpnonce', true, false);
    }
}