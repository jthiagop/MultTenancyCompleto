<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FielParent extends Model
{
    use HasFactory;

    protected $fillable = [
        'fiel_id',
        'nome_pai',
        'nome_mae',
    ];

    public function fiel()
    {
        return $this->belongsTo(Fiel::class);
    }
}
