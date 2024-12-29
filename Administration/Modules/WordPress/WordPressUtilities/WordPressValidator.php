<?php

namespace Administration\Modules\WordPress\WordPressUtilities;

use Administration\Core\GlobalUtilities\GlobalValidatorUtilities;
use Administration\Core\Managers\LoggerManager;

defined('ABSPATH') || exit;

/**
 * Class WordPressValidator
 *
 * Håndterer validering relateret til admin-grænsefladen og input.
 * 
 * @package Administration\Modules\WordPress\WordPressUtilities
 * @since WordPress 1.0.0
 * @version 1.0.0
 *   
 */
class WordPressValidator {

    /**
     * Validerer nonce for sikkerhed.
     *
     * @param string $nonce   Nonce der skal valideres.
     * @param string $action  Action for nonce validering.
     * @return bool True hvis valid, ellers false.
     */
    public static function validate_nonce(string $nonce, string $action): bool {
        if (!wp_verify_nonce($nonce, $action)) {
            LoggerManager::getInstance()->error('Invalid nonce. Validation failed.', [
                'nonce' => $nonce,
                'action' => $action,
            ]);
            return false;
        }

        LoggerManager::getInstance()->info('Nonce validated successfully.', ['action' => $action]);
        return true;
    }

    /**
     * Validerer admin brugerrettigheder.
     *
     * @param string $capability Nødvendig brugerrettighed.
     * @return bool True hvis brugeren har rettighed, ellers false.
     */
    public static function validate_admin_permissions(string $capability = 'manage_options'): bool {
        if (!current_user_can($capability)) {
            LoggerManager::getInstance()->error('Permission denied for current user.', ['capability' => $capability]);
            return false;
        }

        LoggerManager::getInstance()->info('Admin permissions validated successfully.', ['capability' => $capability]);
        return true;
    }

    /**
     * Validerer inputfelter fra admin-formularer.
     *
     * @param array $fields Array af inputfelter, der skal valideres.
     * @return array Array af fejlbeskeder, hvis nogen.
     */
    public static function validate_admin_form_fields(array $fields): array {
        $errors = [];

        foreach ($fields as $field_name => $field_value) {
            if (GlobalValidatorUtilities::is_empty($field_value)) {
                $errors[] = sprintf(__('Field "%s" is required.', 'administration'), $field_name);
                LoggerManager::getInstance()->error(sprintf('Validation error: Field "%s" is empty.', $field_name));
            } elseif (!is_string($field_value)) {
                $errors[] = sprintf(__('Field "%s" must be a valid string.', 'administration'), $field_name);
                LoggerManager::getInstance()->error(sprintf('Validation error: Field "%s" is not a valid string.', $field_name));
            }
        }

        if (empty($errors)) {
            LoggerManager::getInstance()->info('Admin form fields validated successfully.');
        } else {
            LoggerManager::getInstance()->warning('Validation errors occurred for admin form fields.', $errors);
        }

        return $errors;
    }
}
