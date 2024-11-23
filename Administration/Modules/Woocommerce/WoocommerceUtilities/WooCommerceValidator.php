<?php

namespace Administration\Components\Integrations\WooCommerce\Validation;

use Administration\Components\Utilities\GeneralValidator;

class WooCommerceValidator {
    private GeneralValidator $generalValidator;

    public function __construct() {
        $this->generalValidator = new GeneralValidator();
    }

    public function validateDate(string $date): bool {
        return $this->generalValidator->validateDate($date);
    }

    public function validateTime(string $time): bool {
        return $this->generalValidator->validateTime($time);
    }

    // Tilføj WooCommerce-specifik validering, hvis nødvendigt
}
