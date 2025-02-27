<?php

namespace App\Models\Financeiro;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Auth;

class BankStatement extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'bank_statements';

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
        'file_name', // Nome do arquivo OFX
        'file_hash', // Hash do arquivo
        'total_value', // Valor total das transações no OFX
        'transaction_count', // Número de transações
        'imported_at', // Data e hora da importação
        'imported_by', // Usuário que fez a importação
        'created_by',
        'created_by_name',
    ];

    /**
     * 🔗 Relacionamento com Usuário que importou o OFX
     */
    public function importador()
    {
        return $this->belongsTo(User::class, 'imported_by');
    }

    /**
     * 🔗 Relacionamento com transações vinculadas ao extrato
     */
    public function transacoes()
    {
        return $this->belongsToMany(
            TransacaoFinanceira::class,
            'bank_statement_transacao',
            'bank_statement_id',
            'transacao_financeira_id'
        )->withPivot('valor_conciliado', 'status_conciliacao')->withTimestamps();
    }

    /**
     * 🔍 Escopo para buscar apenas lançamentos não conciliados
     */
    public function scopeNaoConciliados($query)
    {
        return $query->where('reconciled', false);
    }

    /**
     * ✅ Método para verificar se um arquivo OFX já foi importado (evita duplicação)
     */
    public static function foiImportadoAntes($fileHash)
    {
        return self::where('file_hash', $fileHash)->exists();
    }

    /**
     * 🔄 Método para armazenar uma nova transação do OFX
     */
    public static function storeTransaction($account, $transaction, $entidadeId)
    {
        // Verifica se a transação já foi importada anteriormente
        $transacaoExistente = self::where('fitid', $transaction->uniqueId)
            ->where('dtposted', self::parseOfxDate($transaction->date))
            ->where('amount', (float) $transaction->amount)
            ->exists();

        if ($transacaoExistente) {
            // Se já existe, não insere duplicado
            return null;
        }

        // Se não existe, insere no banco
        return self::create([
            'company_id'    => Auth::user()->company_id,
            'entidade_financeira_id' => $entidadeId,
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
     * 🔄 Método para conciliar uma transação bancária com um lançamento financeiro
     */
    public function conciliarCom(TransacaoFinanceira $transacao, $valorConciliado)
    {
        // Define status de conciliação com base no valor conciliado
        if (bccomp($valorConciliado, $this->amount, 2) === 0) {
            $status = 'ok';
        } elseif ($valorConciliado < $this->amount) {
            $status = 'parcial';
        } elseif ($valorConciliado > $this->amount) {
            $status = 'divergente';
        } else {
            $status = 'pendente';
        }

        return BankStatementTransacao::create([
            'bank_statement_id'       => $this->id,
            'transacao_financeira_id' => $transacao->id,
            'valor_conciliado'        => $valorConciliado,
            'status_conciliacao'      => $status,
        ]);
    }

    /**
     * 🕒 Método auxiliar para converter datas OFX para formato correto
     */
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
