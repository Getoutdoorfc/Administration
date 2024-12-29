<?php

namespace Administration\Modules\MicroSoft\MicroSoftUtilities;

use Administration\Core\GlobalUtilities\GlobalValidatorUtilities;
use Administration\Core\Managers\LoggerManager;

defined('ABSPATH') || exit;

/**
 * Class MicrosoftValidator
 *
 * Håndterer validering relateret til Microsoft-integration.
 */
class MicrosoftValidator {

    /**
     * Validerer Microsoft credentials.
     *
     * @param string $client_id     Client ID.
     * @param string $client_secret Client Secret.
     * @param string $tenant_id     Tenant ID.
     * @return array Array af fejlbeskeder.
     */
    public static function validate_microsoft_credentials(string $client_id, string $client_secret, string $tenant_id): array {
        LoggerManager::getInstance()->info('Starting validation for Microsoft credentials...');
        $errors = [];

        // Validate Client ID
        if (GlobalValidatorUtilities::is_empty($client_id) || !GlobalValidatorUtilities::validate_format($client_id, '/^[0-9a-fA-F]{8}-[0-9a-fA-F]{4}-[0-9a-fA-F]{4}-[0-9a-fA-F]{4}-[0-9a-fA-F]{12}$/')) {
            $errors[] = __('Client ID is invalid.', 'administration');
        }

        // Validate Client Secret
        if (GlobalValidatorUtilities::is_empty($client_secret) || strlen($client_secret) < 32) {
            $errors[] = __('Client Secret is invalid.', 'administration');
        }

        // Validate Tenant ID
        if (GlobalValidatorUtilities::is_empty($tenant_id) || !GlobalValidatorUtilities::validate_format($tenant_id, '/^[0-9a-fA-F]{8}-[0-9a-fA-F]{4}-[0-9a-fA-F]{4}-[0-9a-fA-F]{4}-[0-9a-fA-F]{12}$/')) {
            $errors[] = __('Tenant ID is invalid.', 'administration');
        }

        if (empty($errors)) {
            LoggerManager::getInstance()->info('Microsoft credentials validated successfully.');
        } else {
            LoggerManager::getInstance()->warning('Validation errors occurred.', $errors);
        }

        return $errors;
    }
}
