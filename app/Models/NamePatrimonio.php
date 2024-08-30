<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class NamePatrimonio extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'cep',
        'logradouro',
        'bairro',
        'localidade',
        'uf',
        'ibge',
        'numForo',
        'complemento',
        'company_id',
        'created_by',
        'updated_by',
    ];

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function editor()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }
}
