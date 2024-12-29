<?php

namespace Administration\Core\GlobalUtilities;

use Administration\Core\Managers\LoggerManager;

defined('ABSPATH') || exit;

/**
 * Class GeneralValidator
 *
 * Indeholder generelle validerings- og sanitiseringsfunktioner.
 * 
 * @package Administration\Core\GlobalUtilities
 * @since 1.0.0
 * @version 1.0.0
 * @see LoggerManager
 */
class GlobalValidatorUtilities {

    /**
     * Generisk valideringsfunktion for formater.
     *
     * @param string $value Værdien der skal valideres.
     * @param string $pattern Regex mønster for validering.
     * @return bool True hvis gyldig, ellers false.
     */
    public static function validate_format(string $value, string $pattern): bool {
        LoggerManager::getInstance()->info('Validating format of the value...');
        return preg_match($pattern, $value) === 1;
    }

    /**
     * Checker om en værdi er tom.
     *
     * @param mixed $value Værdien der skal tjekkes.
     * @return bool True hvis tom, ellers false.
     */
    public static function is_empty($value): bool {
        $trimmed = trim((string)$value);
        LoggerManager::getInstance()->info('Checking if value is empty...', ['value_length' => strlen($trimmed)]);
        return empty($trimmed);
    }

    /**
     * Saniterer input.
     *
     * @param string $input Input der skal saniteres.
     * @return string Saniteret input.
     */
    public static function sanitize_input(string $input): string {
        LoggerManager::getInstance()->info('Sanitizing input...');
        return sanitize_text_field($input);
    }
}
