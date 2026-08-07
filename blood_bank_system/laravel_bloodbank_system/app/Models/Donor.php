<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Donor extends Model
{
    protected $fillable = [
        'user_id',
        'blood_type',
        'age',
        'gender',
        'weight',
        'declaration_confirmed',
        'declaration_confirmed_at',
        'medical_notes',
    ];

    protected $casts = [
        'declaration_confirmed' => 'boolean',
        'declaration_confirmed_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
