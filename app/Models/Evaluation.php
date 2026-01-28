<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Evaluation extends Model
{
    use HasFactory;

    protected $fillable = [
        'application_id',
        'user_id',
        'step_key',
        'overall_score',
        'skill_score',
        'communication_score',
        'culture_fit_score',
        'motivation_score',
        'recommendation',
        'comment',
    ];

    public function application()
    {
        return $this->belongsTo(Application::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
