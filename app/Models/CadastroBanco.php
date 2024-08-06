<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class CadastroBanco extends Model
{
    use HasFactory;

    protected $fillable = [
        'company_id',
        'created_by',
        'banco',
        'name',
        'agencia',
        'conta',
        'digito',
        'account_type',
        'description',
    ];

    // Defina a relação com o modelo User
    public function user()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    static public function geCadastroBanco()
    {
    // Recupere o usuário logado
    $userId = auth()->user()->id; // Recupere o ID do usuário logado

    // Recupera o ID da companhia associada ao usuário logado
    $saidas = DB::table('cadastro_bancos')
    ->join('company_user', 'cadastro_bancos.company_id', '=', 'company_user.company_id')
    ->where('company_user.user_id', $userId)
    ->select('cadastro_bancos.*') // Selecione todas as colunas da tabela 'cadastro_bancos'
    ->get();

return $saidas;
    }
}
