<?php

namespace Administration\Core\Managers;

defined('ABSPATH') || exit;

/**
 * Class SanitizationManager
 *
 * Håndterer sanitering af forskellige typer input i WordPress.
 *
 * @package Administration\Core\Managers
 */
class SanitizationManager
{
    /**
     * Saniterer en tekststreng.
     *
     * @param string $text Tekststrengen, der skal saniteres.
     * @return string Den saniterede tekststreng.
     */
    public static function sanitize_text(string $text): string
    {
        return sanitize_text_field($text);
    }

    /**
     * Saniterer en e-mail-adresse.
     *
     * @param string $email E-mail-adressen, der skal saniteres.
     * @return string Den saniterede e-mail-adresse.
     */
    public static function sanitize_email(string $email): string
    {
        return sanitize_email($email);
    }

    /**
     * Saniterer en URL.
     *
     * @param string $url URL'en, der skal saniteres.
     * @return string Den saniterede URL.
     */
    public static function sanitize_url(string $url): string
    {
        return esc_url_raw($url);
    }

    /**
     * Saniterer en HTML-streng.
     *
     * @param string $html HTML-strengen, der skal saniteres.
     * @return string Den saniterede HTML-streng.
     */
    public static function sanitize_html(string $html): string
    {
        return wp_kses_post($html);
    }

    /**
     * Saniterer en integer.
     *
     * @param int $int Integeren, der skal saniteres.
     * @return int Den saniterede integer.
     */
    public static function sanitize_int(int $int): int
    {
        return filter_var($int, FILTER_SANITIZE_NUMBER_INT);
    }

    /**
     * Saniterer en array af tekststrenge.
     *
     * @param array $array Arrayet af tekststrenge, der skal saniteres.
     * @return array Det saniterede array.
     */
    public static function sanitize_text_array(array $array): array
    {
        return array_map('sanitize_text_field', $array);
    }
}