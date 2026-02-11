<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CandidateMessage extends Model
{
    protected $fillable = [
        'candidate_id',
        'user_id',
        'body',
    ];

    public function candidate()
    {
        return $this->belongsTo(Candidate::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
