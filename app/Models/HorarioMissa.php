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
        'horario' => 'datetime',
    ];

    /**
     * Get the company that owns the mass schedule.
     */
    public function company()
    {
        return $this->belongsTo(Company::class);
    }
}

