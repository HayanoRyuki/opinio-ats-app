<?php

namespace App\Models;

use App\Enums\Role;
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
        'role',       // admin / recruiter / interviewer
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
        'role' => Role::class,
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

    /**
     * ===== Role helpers =====
     */
    public function isVendor(): bool
    {
        return $this->role === Role::Vendor;
    }

    public function isAdmin(): bool
    {
        return $this->role === Role::Admin;
    }

    public function isRecruiter(): bool
    {
        return $this->role === Role::Recruiter;
    }

    public function isInterviewer(): bool
    {
        return $this->role === Role::Interviewer;
    }
}
