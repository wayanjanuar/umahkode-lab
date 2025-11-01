<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Assignment extends Model
{
    use HasFactory;
    protected $fillable = ['key', 'title', 'description', 'template_path'];

    protected $casts = [
        'rubric' => 'array',
        'expected_patterns' => 'array',
        'forbidden_patterns' => 'array',
        'test_cases' => 'array',
    ];


    public function submissions()
    {
        return $this->hasMany(Submission::class);
    }
}
