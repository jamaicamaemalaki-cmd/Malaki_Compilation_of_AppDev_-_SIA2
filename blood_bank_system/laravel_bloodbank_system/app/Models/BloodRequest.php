<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BloodRequest extends Model
{
    protected $fillable = [
        'requester_id',
        'requester_role',
        'patient_name',
        'facility_category',
        'facility_name',
        'blood_type',
        'component',
        'units',
        'urgency',
        'status',
        'reason',
        'admin_note',
    ];

    public function requester()
    {
        return $this->belongsTo(User::class, 'requester_id');
    }
}
