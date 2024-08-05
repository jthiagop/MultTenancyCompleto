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
    $user = auth()->user();

    // Recupera o ID da companhia associada ao usuário logado
    $subsidiary = DB::table('users')
        ->join('company_user', 'users.id', '=', 'company_user.user_id')
        ->join('companies', 'company_user.company_id', '=', 'companies.id')
        ->where('users.id', $user->id) // Filtra pelo usuário logado
        ->select('company_user.company_id')
        ->first();

    // Se a companhia foi encontrada, busca os bancos relacionados a ela
    if ($subsidiary) {
        return CadastroBanco::where('company_id', $subsidiary->company_id)->get();
    }

    // Retorna uma coleção vazia se não houver companhias
    return collect();

    }
}
