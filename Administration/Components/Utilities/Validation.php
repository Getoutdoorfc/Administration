<?php
namespace Administration\Components\Utilities;

use DateTime;

defined('ABSPATH') || exit;

/**
 * Class Validation
 *
 * Indeholder validerings- og sanitiseringsfunktioner for Microsoft API integration.
 *
 * @package Administration\Components\Utilities
 */
final class Validation {

    /**
     * Validerer Client ID format.
     *
     * @param string $client_id
     * @return bool True hvis gyldig, false ellers.
     */
    public static function validate_client_id(string $client_id): bool {
        return preg_match('/^[a-zA-Z0-9\-]+$/', $client_id) === 1;
    }

    /**
     * Validerer Client Secret format.
     *
     * @param string $client_secret
     * @return bool True hvis gyldig, false ellers.
     */
    public static function validate_client_secret(string $client_secret): bool {
        return !empty($client_secret) && is_string($client_secret);
    }

    /**
     * Validerer Tenant ID format.
     *
     * @param string $tenant_id
     * @return bool True hvis gyldig, false ellers.
     */
    public static function validate_tenant_id(string $tenant_id): bool {
        return preg_match('/^[a-zA-Z0-9\-]+$/', $tenant_id) === 1;
    }

    /**
     * Validerer en e-mailadresse.
     *
     * @param string $email E-mailadresse.
     * @return bool True hvis gyldig, false ellers.
     */
    public static function validate_email(string $email): bool {
        return is_email($email) !== false;
    }

    /**
     * Validerer en URL.
     *
     * @param string $url URL der skal valideres.
     * @return bool True hvis gyldig, false ellers.
     */
    public static function validate_url(string $url): bool {
        return filter_var($url, FILTER_VALIDATE_URL) !== false;
    }

    /**
     * Validerer om en værdi er numerisk.
     *
     * @param mixed $value Værdien der skal valideres.
     * @return bool True hvis numerisk, false ellers.
     */
    public static function validate_numeric($value): bool {
        return is_numeric($value);
    }

    /**
     * Saniterer brugerinput.
     *
     * @param mixed $input Input der skal saniteres.
     * @return mixed Saniteret input.
     */
    public static function sanitize_input($input) {
        if (is_array($input)) {
            return array_map(array(__CLASS__, 'sanitize_input'), $input);
        }

        return sanitize_text_field($input);
    }

    /**
     * Validerer en dato i et specifikt format.
     *
     * @param string $date Dato streng.
     * @param string $format Forventet format.
     * @return bool True hvis gyldig, false ellers.
     */
    public static function validate_date(string $date, string $format = 'Y-m-d\TH:i'): bool {
        $d = DateTime::createFromFormat($format, $date);
        return $d && $d->format($format) === $date;
    }
}
