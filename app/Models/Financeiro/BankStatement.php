<?php

namespace App\Models\Financeiro;

use Auth;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class BankStatement extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'bank_statements';

    // Campos que podem ser preenchidos via create()/fill()
    protected $fillable = [
        'company_id',
        'entidade_financeira_id',
        'bank_id',
        'branch_id',
        'account_id',
        'account_type',
        'trntype',
        'dtposted',
        'amount',
        'fitid',
        'checknum',
        'refnum',
        'memo',
        'reconciled',
        'status_conciliacao',

    ];

    //Tabela pivot
    public function transacoes()
    {
        return $this->belongsToMany(
            TransacaoFinanceira::class,
            'bank_statement_transacao',          // Nome da tabela pivot
            'bank_statement_id',                 // FK local
            'transacao_financeira_id'            // FK na pivot p/ rel. invertido
        )
        ->withPivot('valor_conciliado', 'status_conciliacao')
        ->withTimestamps();
    }

    public static function storeTransaction($account, $transaction, $entidadeId)
    {
        return self::create([
            'company_id'    => Auth::user()->company_id,
            'entidade_financeira_id' => $entidadeId, // Relacionamento com `entidades_financeiras`
            'bank_id'       => $account->routingNumber,
            'branch_id'     => $account->agencyNumber,
            'account_id'    => $account->accountNumber,
            'account_type'  => $account->accountType,
            'trntype'       => $transaction->type,
            'dtposted'      => self::parseOfxDate($transaction->date),
            'amount'        => (float) $transaction->amount,
            'fitid'         => $transaction->uniqueId,
            'checknum'      => $transaction->checkNumber,
            'refnum'        => $transaction->referenceNumber ?? null,
            'memo'          => $transaction->memo,
            'reconciled'    => false,

        ]);
    }

    /**
    * Exemplo de método auxiliar para converter string do OFX em data/hora
    */
    // Exemplo de função de parsing
    private static function parseOfxDate($ofxDateString)
    {
        if ($ofxDateString instanceof \DateTime) {
            $ofxDateString->setTimezone(new \DateTimeZone('America/Sao_Paulo'));
            return $ofxDateString->format('Y-m-d H:i:s');
        }

        if (is_string($ofxDateString)) {
            $dateString = substr($ofxDateString, 0, 14);
            $dt = new \DateTime(substr($dateString, 0, 4) . '-' . substr($dateString, 4, 2) . '-' . substr($dateString, 6, 2) .
                ' ' . substr($dateString, 8, 2) . ':' . substr($dateString, 10, 2) . ':' . substr($dateString, 12, 2));
            $dt->setTimezone(new \DateTimeZone('America/Sao_Paulo'));
            return $dt->format('Y-m-d H:i:s');
        }

        return now()->format('Y-m-d H:i:s');
    }
}
