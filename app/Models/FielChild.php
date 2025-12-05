<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FielChild extends Model
{
    use HasFactory;

    protected $fillable = [
        'fiel_id',
        'nome',
        'data_nascimento',
        'estado_civil',
        'sexo',
    ];

    protected $casts = [
        'data_nascimento' => 'date',
    ];

    public function fiel()
    {
        return $this->belongsTo(Fiel::class);
    }
}
