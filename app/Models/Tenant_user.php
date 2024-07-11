<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Tenant_user extends Model
{
    use HasFactory;
    protected $fillable =
    [
        'user_id',
        'tenant_id'
    ];


    public function users() {
        return $this->belongsToMany(User::class);
    }
}
