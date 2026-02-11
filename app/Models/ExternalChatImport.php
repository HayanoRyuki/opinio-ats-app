<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ExternalChatImport extends Model
{
    protected $fillable = [
        'candidate_id',
        'source',
        'source_label',
        'raw_text',
        'summary',
        'imported_by',
        'sender_name',
    ];

    public function candidate()
    {
        return $this->belongsTo(Candidate::class);
    }
}
