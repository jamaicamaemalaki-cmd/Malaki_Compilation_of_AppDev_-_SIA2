<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BloodInventory extends Model
{
    protected $fillable = [
        'medical_facility_id',
        'facility_name',
        'blood_type',
        'component',
        'units_available',
        'notes',
    ];
}
