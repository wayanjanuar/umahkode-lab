<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Score extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'pemahaman_kasus',
        'langkah_teknis',
        'ketepatan_hasil',
        'analisa_laporan',
        'manajemen_waktu',
        'score_total',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
