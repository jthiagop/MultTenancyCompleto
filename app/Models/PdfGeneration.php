<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PdfGeneration extends Model
{
    use HasFactory;

    protected $fillable = [
        'type',
        'user_id',
        'company_id',
        'status',
        'filename',
        'parameters',
        'error_message',
        'started_at',
        'completed_at',
    ];

    protected $casts = [
        'parameters' => 'array',
        'started_at' => 'datetime',
        'completed_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(\App\Models\User::class);
    }

    public function company()
    {
        return $this->belongsTo(\App\Models\Company::class);
    }

    public function getDownloadUrlAttribute()
    {
        if ($this->status === 'completed' && $this->filename) {
            return \Storage::disk('public')->url($this->filename);
        }
        return null;
    }
}
