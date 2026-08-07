<?php

namespace App\Services;

class BloodComponents
{
    public static function all(): array
    {
        return ['Whole Blood', 'Platelets', 'Plasma'];
    }
}
