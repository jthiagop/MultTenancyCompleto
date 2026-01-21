<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BankStatementEntry extends Model
{
    use HasFactory;

    protected $fillable = [
        'company_id',
        'import_id',
        'posted_at',
        'description',
        'document_number',
        'amount',
        'type',
        'amount_signed',
        'balance_after',
        'unique_hash',
        'status_conciliacao',
        'bank_metadata',
    ];

    protected $casts = [
        'posted_at' => 'date',
        'amount' => 'decimal:2',
        'amount_signed' => 'decimal:2',
        'balance_after' => 'decimal:2',
        'bank_metadata' => 'array',
    ];

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function import(): BelongsTo
    {
        return $this->belongsTo(BankStatementImport::class, 'import_id');
    }

    /**
     * Gera hash único para o lançamento
     */
    public static function generateHash(string $date, float $amount, string $type, ?string $document = null): string
    {
        return md5($date . $amount . $type . ($document ?? ''));
    }

    /**
     * Scope para filtrar por status de conciliação
     */
    public function scopePendente($query)
    {
        return $query->where('status_conciliacao', 'pendente');
    }

    public function scopeConciliado($query)
    {
        return $query->where('status_conciliacao', 'conciliado');
    }
}
