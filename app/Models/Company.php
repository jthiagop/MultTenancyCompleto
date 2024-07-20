<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Company extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'cnpj',
        'email',
        'avatar',
        'data_cnpj',
        'data_fundacao',
        'details',
        'type',
        'parent_id',
        'status',
        'tags',
        'created_by',
        'updated_by',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'tags' => 'array',
    ];

    /**
     * Get the parent company if this company is a branch.
     */
    // Relação de filiais
    public function filials()
    {
        return $this->hasMany(Company::class, 'parent_id');
    }

    // Relação de matriz
    public function parent()
    {
        return $this->belongsTo(Company::class, 'parent_id');
    }

    // Relação com os usuários
    public function users()
    {
        return $this->belongsToMany(User::class);
    }

    /**
     * Get the branches of this company if it is a parent.
     */
    public function branches()
    {
        return $this->hasMany(Company::class, 'parent_id');
    }

    /**
     * Get the user who created the company.
     */
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Get the user who last updated the company.
     */
    public function updater()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    public function addresses()
    {
        return $this->hasOne(Adress::class, 'company_id');
    }
}
