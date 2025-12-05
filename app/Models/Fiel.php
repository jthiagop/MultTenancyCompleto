<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Fiel extends Model
{
    use HasFactory;

    protected $table = 'fieis';

    protected $fillable = [
        'company_id',
        'nome_completo',
        'data_nascimento',
        'sexo',
        'cpf',
        'rg',
        'notifications',
        'status',
        'created_by',
        'updated_by',
        'created_by_name',
        'updated_by_name',
        'avatar'
    ];

    protected $casts = [
        'data_nascimento' => 'date',
        'notifications' => 'array',
    ];

    // Relacionamentos
    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function addresses()
    {
        return $this->belongsToMany(Address::class, 'fiel_address', 'fiel_id', 'address_id')
            ->withPivot('tipo')
            ->withTimestamps();
    }

    public function contacts()
    {
        return $this->hasMany(FielContact::class);
    }

    public function complementaryData()
    {
        return $this->hasOne(FielComplementaryData::class);
    }

    public function parents()
    {
        return $this->hasOne(FielParent::class);
    }

    public function spouse()
    {
        return $this->hasOne(FielSpouse::class);
    }

    public function children()
    {
        return $this->hasMany(FielChild::class);
    }

    public function religiousData()
    {
        return $this->hasOne(FielReligiousData::class);
    }

    public function tithe()
    {
        return $this->hasOne(FielTithe::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updater()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }
}
