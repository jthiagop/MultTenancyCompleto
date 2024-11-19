<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use App\Models\User;

class LancamentoPadrao extends Model
{
    use HasFactory;

    protected $table = 'lancamento_padraos';
    protected $primaryKey = 'id';
    public $incrementing = true;
    protected $keyType = 'int';

    protected $fillable = [
        'type',
        'description',
        'date',
        'category',
        'user_id',
        'created_at'
    ];

    protected $casts = [
        'date' => 'datetime',
        'created_at' => 'datetime'
    ];

    // Relacionamento com o usuário que criou o lançamento padrão
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Relacionamento com caixas que utilizam este lançamento padrão
    public function caixas()
    {
        return $this->hasMany(Caixa::class, 'lancamento_padrao_id');
    }
}
