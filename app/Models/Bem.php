<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Bem extends Model
{
    use HasFactory;

    protected $table = 'bens';

    protected $fillable = [
        'company_id',
        'created_by',
        'descricao',
        'tipo',
        'centro_custo',
        'valor',
        'data_aquisicao',
        'numero_documento',
        'fornecedor',
        'depreciar',
        'estado_bem',
        'dados_adicionais',
    ];

    protected $casts = [
        'valor' => 'decimal:2',
        'data_aquisicao' => 'date',
        'depreciar' => 'boolean',
        'dados_adicionais' => 'array',
    ];

    /**
     * Relacionamento com Veículo (1:1)
     */
    public function veiculo()
    {
        return $this->hasOne(Veiculo::class, 'bem_id');
    }

    /**
     * Relacionamento com Imóvel (1:1)
     */
    public function imovel()
    {
        return $this->hasOne(Imovel::class, 'bem_id');
    }

    /**
     * Relacionamento com Bem Móvel (1:1)
     */
    public function bemMovel()
    {
        return $this->hasOne(BemMovel::class, 'bem_id');
    }

    /**
     * Relacionamento com Company
     */
    public function company()
    {
        return $this->belongsTo(Company::class, 'company_id');
    }

    /**
     * Relacionamento com User (criador)
     */
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Scope para filtrar por tipo
     */
    public function scopeTipo($query, $tipo)
    {
        return $query->where('tipo', $tipo);
    }

    /**
     * Scope para filtrar por company
     */
    public function scopeCompany($query, $companyId)
    {
        return $query->where('company_id', $companyId);
    }
}
