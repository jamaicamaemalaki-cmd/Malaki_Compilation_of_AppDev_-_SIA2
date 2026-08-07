<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Donation extends Model
{
    protected $fillable = [
        'donor_id',
        'blood_type',
        'component',
        'units',
        'donation_date',
        'facility_name',
        'notes',
    ];

    public function donor()
    {
        return $this->belongsTo(Donor::class);
    }
}
