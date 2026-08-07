<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DonationRequest extends Model
{
    protected $fillable = [
        'donor_id',
        'facility_category',
        'facility_name',
        'blood_type',
        'component',
        'units',
        'status',
        'scheduled_date',
        'start_time',
        'end_time',
        'donor_note',
        'facility_note',
    ];

    protected function casts(): array
    {
        return [
            'scheduled_date' => 'date',
        ];
    }

    public function formattedStartTime(): ?string
    {
        return $this->start_time ? substr((string) $this->start_time, 0, 5) : null;
    }

    public function formattedEndTime(): ?string
    {
        return $this->end_time ? substr((string) $this->end_time, 0, 5) : null;
    }

    public function donor()
    {
        return $this->belongsTo(Donor::class);
    }
}
