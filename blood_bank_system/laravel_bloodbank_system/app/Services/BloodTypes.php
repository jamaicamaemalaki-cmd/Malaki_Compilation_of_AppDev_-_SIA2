<?php

namespace App\Services;

class BloodTypes
{
    public static function all(): array
    {
        return ['A+', 'A-', 'B+', 'B-', 'AB+', 'AB-', 'O+', 'O-'];
    }
}
