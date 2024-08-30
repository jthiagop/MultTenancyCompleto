<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Escritura extends Model
{
    use HasFactory;

    protected $fillable = [
        'outorgante',
        'matricula',
        'aquisicao',
        'outorgado',
        'valor_aquisicao',
        'area_total',
        'area_privativa',
        'informacoes',
        'patrimonio_id',
        'created_by',
        'updated_by',
    ];

    /**
     * Relacionamento com Patrimonio.
     * Cada escritura pertence a um único patrimônio.
     */
    public function patrimonio()
    {
        return $this->belongsTo(Patrimonio::class, 'patrimonio_id');
    }

    /**
     * Relacionamento com User para created_by.
     * Usuário que criou o registro.
     */
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Relacionamento com User para updated_by.
     * Usuário que atualizou o registro.
     */
    public function updater()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }
}
