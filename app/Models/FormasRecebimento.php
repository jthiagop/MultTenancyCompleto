<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class FormasRecebimento extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'formas_recebimento';

    protected $fillable = [
        'nome',
        'codigo',
        'ativo',
        'observacao',
    ];

    protected $hidden = [
        'created_at',
        'updated_at',
    ];
}
