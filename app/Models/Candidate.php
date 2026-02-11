<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Candidate extends Model
{
    use HasFactory;

    protected $fillable = [
        'company_id',
        'name',
        'email',
        'phone',
        'memo',
    ];

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function applications()
    {
        return $this->hasMany(Application::class);
    }

    public function person()
    {
        return $this->belongsTo(Person::class);
    }

    public function messages()
    {
        return $this->hasMany(CandidateMessage::class);
    }

    public function externalChatImports()
    {
        return $this->hasMany(ExternalChatImport::class);
    }
}
