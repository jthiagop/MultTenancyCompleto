<?php

namespace App\Models\Cemiterio;


use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Sepultura extends Model
{
    use HasFactory, SoftDeletes;
        // Atributo para a data de exclusão
        protected $dates = ['deleted_at'];

    protected $fillable = [
        'company_id',
        'codigo_sepultura',
        'localizacao',
        'tipo',
        'tamanho',
        'data_aquisicao',
        'status',
        'created_by',
        'created_by_name',
        'updated_by',
        'updated_by_name',
    ];


}
