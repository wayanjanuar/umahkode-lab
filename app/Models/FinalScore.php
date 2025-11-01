<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FinalScore extends Model
{
    protected $fillable = [
        'user_id',
        'completed_assignments',
        'total_assignments',
        'average_percent',
        'scale_1_5',
        'details',
        'generated_at',
    ];

    protected $casts = [
        'details' => 'array',
        'generated_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
