<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Adress extends Model
{
    use HasFactory;

    protected $fillable =
    [
        'tenant_id',
        'cep',
        'rua',
        'complemento',
        'cidade',
        'numero',
        'bairro',
        'uf',
    ];

    public function tenant()
    {
        return $this->hasOne( TenantFilial::class);
    }
}
