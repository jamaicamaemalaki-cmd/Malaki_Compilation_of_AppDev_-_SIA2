<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MedicalFacility extends Model
{
    protected $fillable = [
        'user_id',
        'facility_category',
        'facility_name',
        'license_number',
        'contact_person',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
