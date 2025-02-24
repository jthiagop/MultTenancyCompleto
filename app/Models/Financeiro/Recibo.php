<?php

namespace App\Models\Financeiro;

use Illuminate\Database\Eloquent\Model;
use App\Models\Address;

class Recibo extends Model
{
    protected $fillable = [
        'transacao_id',
        'address_id',
        'numero_recibo',
        'data_emissao',
        'nome',
        'cpf_cnpj',
        'endereco',
        'cidade',
        'estado',
        'valor',
        'referente'];

    public function transacao()
    {
        return $this->belongsTo(TransacaoFinanceira::class, 'transacao_id');
    }

    public function address()
    {
        return $this->belongsTo(Address::class, 'address_id');
    }
}
