<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Application;

class SelectionStep extends Model
{
    use HasFactory;

    protected $fillable = [
        'company_id',
        'key',
        'label',
        'order',
        'is_active',
    ];

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    /**
     * このステップにある応募一覧
     */
    public function applications()
    {
        return Application::where('company_id', $this->company_id)
            ->where('status', $this->key);
    }
}
