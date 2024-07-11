<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
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
        'avatar'
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

    public function getAvatarUrlAttribute()
    {
        if ($this->avatar) {
            return route('avatar.show', [tenancy()->tenant->id, $this->avatar]);
        }

        return asset('path/to/default/avatar.jpg');
    }


        // Adicionar um acessor para `last_login_formatted`
        public function getLastLoginFormattedAttribute()
        {
            return $this->last_login ? Carbon::parse($this->last_login)->diffForHumans() : 'Nunca';
        }
}
