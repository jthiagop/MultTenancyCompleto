<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HorarioMissa extends Model
{
    use HasFactory;

    protected $table = 'horarios_missas';

    protected $fillable = [
        'company_id',
        'dia_semana',
        'horario',
        'intervalo'
    ];

    protected $casts = [
        'horario' => 'string', // Mantém como string para evitar problemas com time-only values
        'intervalo' => 'integer',
    ];

    /**
     * Get the company that owns the mass schedule.
     */
    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    /**
     * Relacionamento com bank statements conciliados
     */
    public function bankStatements()
    {
        return $this->hasMany(\App\Models\Financeiro\BankStatement::class, 'horario_missa_id');
    }
}

