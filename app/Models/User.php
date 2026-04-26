<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\URL;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;


class User extends Authenticatable
{
    use HasFactory, Notifiable, HasRoles, HasApiTokens;

    /**
     * The attributes that are mass assignable.
     *
     * Campos sens?veis como `active`, `must_change_password`, `company_id`,
     * `password_changed_at`, `last_login` foram REMOVIDOS do fillable para
     * evitar escalonamento de privil?gio via mass-assignment
     * (ex.: update($request->all())). Eles devem ser atribu?dos explicitamente,
     * via $user->campo = valor; $user->save();
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'avatar',
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
            'active' => 'boolean',
            'must_change_password' => 'boolean',
            'password_changed_at' => 'datetime',

        ];
    }

    public function tenant_filials()
    {
        return $this->belongsTo(TenantFilial::class);
    }

    /**
     * Sobrescreve o relacionamento padr?o do trait Notifiable para usar
     * o model customizado AppNotification ? que sincroniza colunas
     * f?sicas (company_id, title, message, channel, meta, sent_at)
     * automaticamente no creating().
     */
    public function notifications()
    {
        return $this->morphMany(\App\Models\AppNotification::class, 'notifiable')->latest();
    }

    /**
     * Mant?m o contrato do trait Notifiable (readNotifications/unreadNotifications)
     * apontando para o model customizado.
     */
    public function readNotifications()
    {
        return $this->notifications()->whereNotNull('read_at');
    }

    public function unreadNotifications()
    {
        return $this->notifications()->whereNull('read_at');
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

    /**
     * Retorna a URL completa do avatar do usuário.
     */
    public function getAvatarUrlAttribute(): ?string
    {
        if (!$this->avatar || $this->avatar === 'tenant/blank.png') {
            return null;
        }

        // Se já for uma URL completa, retorna como está
        if (str_starts_with($this->avatar, 'http://') || str_starts_with($this->avatar, 'https://')) {
            return $this->avatar;
        }

        // Remove prefixos desnecessários
        $path = ltrim($this->avatar, '/');
        if (str_starts_with($path, 'public/')) {
            $path = substr($path, strlen('public/'));
        }

        // Usa a rota /file/ do tenant que serve corretamente do storage isolado
        return '/file/' . $path;
    }

    /**
     * As empresas ��s quais o usuário tem acesso.
     */
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

    // Favoritos de rotas
    public function favoriteRoutes()
    {
        return $this->hasMany(UserFavoriteRoute::class)
            ->where('company_id', session('active_company_id'))
            ->ordered();
    }

    // Helper para favoritos com permissão
    public function getAuthorizedFavoritesAttribute()
    {
        return $this->favoriteRoutes->filter(function ($favorite) {
            return $this->can($favorite->module_key . '.index');
        });
    }

    static public function getCompany()
    {
        // Recupere o usuário logado
        $user = Auth::user();

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
        $user = Auth::user();

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
