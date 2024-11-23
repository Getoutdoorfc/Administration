<?php

namespace Administration\Components\Utilities\Contracts;

interface ValidatorInterface {
    public function validate($data): bool; // Generel valideringsmetode
}
