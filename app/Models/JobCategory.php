<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class JobCategory extends Model
{
    protected $fillable = [
        'company_id',
        'key',
        'label',
        'order',
        'is_active',
    ];

    public $incrementing = true;
    protected $keyType = 'int';
}
