<?php

namespace App\Models;

class Company extends BaseModel
{
    protected $table = 'companies';

protected $fillable = [
    'name',
    'domain',    
    'address',
    'phone',
    'website',
    'industry',
];
}


