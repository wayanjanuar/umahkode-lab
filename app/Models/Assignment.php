<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Assignment extends Model
{
    use HasFactory;
    protected $fillable = ['key','title','description','template_path'];

    public function submissions(){
        return $this->hasMany(Submission::class);
    }
}
