<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Evaluation extends Model
{
    use HasFactory;

    protected $fillable = [
        'submission_id',
        'score',
        'components',
        'feedback',
    ];

    // 👇 Tambahkan ini
    protected $casts = [
        'components' => 'array',
        'artifacts'  => 'array',
    ];

    public function submission()
    {
        return $this->belongsTo(Submission::class);
    }
}
