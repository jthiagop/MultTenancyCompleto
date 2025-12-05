<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FielReligiousData extends Model
{
    use HasFactory;

    protected $table = 'fiel_religious_data';

    protected $fillable = [
        'fiel_id',
        'data_batismo',
        'local_batismo',
        'data_casamento',
        'local_casamento',
        'data_ingresso',
        'responsavel_ingresso',
        'grupo_participante',
        'ministerio',
        'comunidade_id',
    ];

    protected $casts = [
        'data_batismo' => 'date',
        'data_casamento' => 'date',
        'data_ingresso' => 'date',
    ];

    public function fiel()
    {
        return $this->belongsTo(Fiel::class);
    }
}
