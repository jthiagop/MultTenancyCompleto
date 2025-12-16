<?php

namespace App\Models;

use Stancl\Tenancy\Database\Models\Tenant as BaseTenant;
use Stancl\Tenancy\Contracts\TenantWithDatabase;
use Stancl\Tenancy\Database\Concerns\HasDatabase;
use Stancl\Tenancy\Database\Concerns\HasDomains;
use OwenIt\Auditing\Contracts\Auditable;


class Tenant extends BaseTenant implements TenantWithDatabase, Auditable
{
    use HasDatabase, HasDomains;
    use \OwenIt\Auditing\Auditable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'app_access_code',
    ];

    public static function getCustomColumns(): array
    {
        return[
            'id',
            'name',
            'email',
            'password',
            'app_access_code'
        ];
    }

    public function setPasswordAttribute($value){
        return $this->attributes['password'] = bcrypt($value);
    }

    /**
     * Gera um código de acesso único para o aplicativo mobile
     * Formato: DOM-XXXXX (onde X são 5 caracteres alfanuméricos maiúsculos)
     *
     * @return string
     */
    public function generateAppCode(): string
    {
        // Se já existe código, retornar o atual
        if ($this->app_access_code) {
            return $this->app_access_code;
        }

        // Gerar código único no formato DOM-XXXXX
        // O modelo Tenant sempre usa o banco central, então não precisa de tenancy()->central()
        do {
            $randomCode = strtoupper(substr(str_shuffle('ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789'), 0, 5));
            $code = 'DOM-' . $randomCode;

            // Verificar se o código já existe no banco central
            // O modelo Tenant sempre consulta o banco central automaticamente
            $exists = static::where('app_access_code', $code)
                ->where('id', '!=', $this->id)
                ->exists();
        } while ($exists);

        // Salvar o código gerado
        $this->app_access_code = $code;
        $this->save();

        return $code;
    }

}
