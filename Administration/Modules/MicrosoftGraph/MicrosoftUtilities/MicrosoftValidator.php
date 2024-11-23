<?php

namespace Administration\Components\Integrations\Microsoft;

use Administration\Components\Utilities\GeneralValidator;
use Administration\Components\Utilities\Logger;

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
        Logger::getInstance()->info('Starting validation for Microsoft credentials...');
        $errors = [];

        // Validate Client ID
        if (GeneralValidator::is_empty($client_id) || !GeneralValidator::validate_format($client_id, '/^[0-9a-fA-F]{8}-[0-9a-fA-F]{4}-[0-9a-fA-F]{4}-[0-9a-fA-F]{4}-[0-9a-fA-F]{12}$/')) {
            $errors[] = __('Client ID is invalid.', 'administration');
        }

        // Validate Client Secret
        if (GeneralValidator::is_empty($client_secret) || strlen($client_secret) < 32) {
            $errors[] = __('Client Secret is invalid.', 'administration');
        }

        // Validate Tenant ID
        if (GeneralValidator::is_empty($tenant_id) || !GeneralValidator::validate_format($tenant_id, '/^[0-9a-fA-F]{8}-[0-9a-fA-F]{4}-[0-9a-fA-F]{4}-[0-9a-fA-F]{4}-[0-9a-fA-F]{12}$/')) {
            $errors[] = __('Tenant ID is invalid.', 'administration');
        }

        if (empty($errors)) {
            Logger::getInstance()->info('Microsoft credentials validated successfully.');
        } else {
            Logger::getInstance()->warning('Validation errors occurred.', $errors);
        }

        return $errors;
    }
}
