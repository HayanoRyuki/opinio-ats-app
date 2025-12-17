<?php

namespace App\Models;

class Company extends BaseModel
{
    protected $table = 'companies';

    protected $fillable = [
        'name',
        'slug',
        'description',
        'mission',
        'culture',
        'logo_url',
    ];
}
