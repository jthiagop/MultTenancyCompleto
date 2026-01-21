<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class BankStatementImport extends Model
{
    use HasFactory;

    protected $fillable = [
        'company_id',
        'bank_config_id', // ID da configuração do BB usada para importar
        // 'bank_account_id', // Removido temporariamente para teste
        'source',
        'file_name',
        'file_hash',
        'period_start',
        'period_end',
        'imported_by',
        'imported_at',
    ];

    protected $casts = [
        'period_start' => 'date',
        'period_end' => 'date',
        'imported_at' => 'datetime',
    ];

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function bankConfig(): BelongsTo
    {
        return $this->belongsTo(BankConfig::class);
    }

    public function importedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'imported_by');
    }

    public function entries(): HasMany
    {
        return $this->hasMany(BankStatementEntry::class, 'import_id');
    }
}
