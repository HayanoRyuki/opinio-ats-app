<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    /**
     * Mass assignable attributes
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',       // employee / recruiter など
        'company_id', // 所属会社ID
    ];

    /**
     * Hidden attributes
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Casts
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    /**
     * 所属する会社
     */
    public function company()
    {
        return $this->belongsTo(\App\Models\Company::class);
    }

    /**
     * このユーザーが応募した応募一覧
     */
    public function applications()
    {
        return $this->hasMany(\App\Models\Application::class);
    }
}
