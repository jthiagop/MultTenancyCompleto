<?php

namespace App\Models;

use App\Models\Financeiro\Recibo;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Address extends Model
{
        // Especifica o nome da tabela
        protected $table = 'adresses'; // Nome da tabela no banco de dados

    use HasFactory;

    protected $fillable =
    [
        'company_id',
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

    public function company()
    {
        return $this->hasOne( Company::class);
    }

    public function recibos()
    {
        return $this->hasMany(Recibo::class, 'address_id');
    }

    public function fieis()
    {
        return $this->belongsToMany(Fiel::class, 'fiel_address', 'address_id', 'fiel_id')
            ->withPivot('tipo')
            ->withTimestamps();
    }
}
