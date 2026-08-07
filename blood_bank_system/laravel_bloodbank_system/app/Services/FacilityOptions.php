<?php

namespace App\Services;

class FacilityOptions
{
    public static function all(): array
    {
        return [
            'Hospital' => [
                'Hinunangan Community Hospital',
                'Zenon T. Lagumbay Memorial Hospital',
            ],
            'Rural Health Unit' => [
                'Hinunangan Rural Health Unit',
            ],
            'Red Cross' => [
                'Philippine Red Cross-Southern Leyte Chapter',
            ],
        ];
    }

    public static function names(): array
    {
        return collect(self::all())->flatten()->values()->all();
    }
}
