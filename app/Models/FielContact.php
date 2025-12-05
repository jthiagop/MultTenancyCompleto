<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FielContact extends Model
{
    use HasFactory;

    protected $fillable = [
        'fiel_id',
        'tipo',
        'valor',
        'principal',
    ];

    protected $casts = [
        'principal' => 'boolean',
    ];

    public function fiel()
    {
        return $this->belongsTo(Fiel::class);
    }
}
