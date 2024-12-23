<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Anexo extends Model
{
    use HasFactory;

    protected $fillable = ['caixa_id','banco_id', 'nome_arquivo', 'size', 'caminho_arquivo', 'created_by', 'updated_by'];

    public function caixa()
    {
        return $this->belongsTo(Caixa::class);
    }

    public function banco()
    {
        return $this->belongsTo(Banco::class, 'id');
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updatedBy()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }
}
