<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\URL;
use Spatie\Permission\Traits\HasRoles;


class User extends Authenticatable
{
    use HasFactory, Notifiable, HasRoles;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'avatar',
        'company_id',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function tenant_filials()
    {
        return $this->belongsTo(TenantFilial::class);
    }

    public function posts()
    {
        return $this->hasMany(Post::class);
    }

    public function filiais()
    {
        return $this->belongsToMany(TenantFilial::class, 'tenant_users', 'user_id', 'tenant_id');
    }


        // Adicionar um acessor para `last_login_formatted`
        public function getLastLoginFormattedAttribute()
        {
            return $this->last_login ? Carbon::parse($this->last_login)->diffForHumans() : 'Nunca';
        }

        public function companies()
        {
            return $this->belongsToMany(Company::class, 'company_user');
        }

        public function company()
        {
            return $this->belongsTo(Company::class, 'company_id');
        }


        public function bancos()
        {
            return $this->hasMany(CadastroBanco::class, 'created_by');
        }

        static public function getCompany()
        {
            // Recupere o usuário logado
            $user = auth()->user();

            $subsidiaryId = DB::table('users')
            ->join('company_user', 'users.id', '=', 'company_user.user_id')
            ->join('companies', 'company_user.company_id', '=', 'companies.id')
            ->where('users.id', $user->id) // Filtra pelo usuário logado
            ->select('company_user.company_id')
            ->first();

            return $subsidiaryId;

        }

        static public function getCompanyName()
        {
                    // Recupere o usuário logado
        $user = auth()->user();

        // Filtrar os usuários pelo usuário logado
        $company = DB::table('users')
            ->join('company_user', 'users.id', '=', 'company_user.user_id')
            ->join('companies', 'company_user.company_id', '=', 'companies.id')
            ->where('users.id', $user->id) // Filtra pelo usuário logado
            ->select('users.*', 'company_user.company_id', 'companies.name as companies_name')
            ->get();

            return $company;
        }
}
