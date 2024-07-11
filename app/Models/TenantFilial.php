<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Webpatser\Uuid\Uuid;

class TenantFilial extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'photo'
    ];

    public static function boot()
    {
        parent::boot();

        self::creating(function ($model) {
            $model->uuid = Uuid::generate(4);
        });
    }
    public function user()
    {
        return $this->hasMany(User::class);
    }

    public function addresses()
    {
        return $this->hasOne(Adress::class, 'tenant_id');
    }

    //Recuperar Tenant Associado
    static public function getcompany()
    {
        // Recupere o usuário logado
        $user = auth()->user();


        // Filtrar os usuários pelo usuário logado
        $filial = DB::table('users')
            ->join('tenant_users', 'users.id', '=', 'tenant_users.user_id')
            ->join('tenant_filials', 'tenant_users.tenant_id', '=', 'tenant_filials.id')
            ->where('users.id', $user->id) // Filtra pelo usuário logado
            ->select('users.*', 'tenant_users.tenant_id', 'tenant_filials.name as tenant_name')
            ->first(); // Usamos first() para obter apenas um registro


        if (is_object($filial)) {
            // O relacionamento com o tenant existe
            $filial = $filial->tenant_name;
            // Faça o que você precisa com $tenantName
        } else {
            $filial = 'Administração';
        }

        return $filial;
    }
}
