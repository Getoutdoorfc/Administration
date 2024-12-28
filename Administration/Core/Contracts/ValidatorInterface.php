<?php

namespace Administration\Core\Contracts;

/**
 * Class StringHelper
 *
 * Hjælpeklasse til strengmanipulation.
 *
 * @package Administration\Components\Utilities
 */

interface ValidatorInterface {
    public function validate($data): bool; // Generel valideringsmetode
}
