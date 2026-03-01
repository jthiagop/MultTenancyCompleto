<?php

namespace App\Models\Financeiro;

use App\Models\User;
use App\Models\EntidadeFinanceira;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;

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
        'amount_cents', // ✅ Novo campo para centavos
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
        'transaction_datetime', // Datetime final utilizado na lógica
        'source_time', // Origem do horário ('memo' ou 'dtposted')
        'conciliado_com_missa', // Flag de conciliação automática
        'horario_missa_id', // FK para horarios_missas
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
     * 🔗 Relacionamento com horário de missa
     */
    public function horarioMissa()
    {
        return $this->belongsTo(\App\Models\HorarioMissa::class, 'horario_missa_id');
    }

    /**
     * 🔍 Escopo para buscar apenas lançamentos não conciliados
     */
    public function scopeNaoConciliados($query)
    {
        return $query->where('reconciled', false);
    }

    /**
     * 🔍 Escopo para buscar transações conciliadas com missas
     */
    public function scopeConciliadosComMissas($query)
    {
        return $query->where('conciliado_com_missa', true);
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
    public static function storeTransaction($account, $transaction, $entidadeId, $fileHash = null, $fileName = null, $companyId = null)
    {
        // ✅ Usa Money::fromOfx para converter valor do OFX (pode ser negativo)
        $money = \App\Support\Money::fromOfx((float) $transaction->amount);
        // ✅ CORREÇÃO: Preservar o sinal negativo para débitos (pagamentos)
        // Débitos (pagamentos) vêm como negativos no OFX e DEVEM ser salvos negativos
        $amountValue = round($money->getSignedAmount(), 2); // DECIMAL com sinal
        $amountCents = (int) round($money->getSignedAmount() * 100); // Integer em centavos com sinal

        // ✅ Usa companyId passado como parâmetro (evita query N+1)
        // Fallback para busca na entidade apenas se não foi informado
        if (!$companyId) {
            $entidade = \App\Models\EntidadeFinanceira::find($entidadeId);
            $companyId = $entidade?->company_id ?? session('active_company_id') ?? Auth::user()?->company_id;
        }

        // ✅ Usa firstOrCreate com chave composta para garantir unicidade
        // Mesmo arquivo (file_hash igual) pode ter múltiplas transações (fitid diferente)
        $bankStatement = self::firstOrCreate(
            [
                // Chave composta: essas combinações devem ser únicas
                'fitid' => $transaction->uniqueId,
                'dtposted' => self::parseOfxDate($transaction->date),
                'entidade_financeira_id' => $entidadeId,
            ],
            [
                // Dados adicionais inseridos apenas se o registro não existir
                'company_id'    => $companyId,
                'bank_id'       => $account->routingNumber,
                'branch_id'     => $account->agencyNumber,
                'account_id'    => $account->accountNumber,
                'account_type'  => $account->accountType,
                'trntype'       => $transaction->type,
                'amount'        => $amountValue,
                'amount_cents'  => $amountCents, // ✅ Novo: salvar em centavos (integer)
                'checknum'      => $transaction->checkNumber,
                'refnum'        => $transaction->referenceNumber ?? null,
                'memo'          => $transaction->memo,
                'reconciled'    => false,
                'status_conciliacao' => 'pendente', // ✅ Status inicial padrão
                'file_hash'     => $fileHash, // Hash do arquivo (múltiplas transações do mesmo arquivo)
                'file_name'     => $fileName, // Nome do arquivo
                'imported_at'   => now(), // Data/hora da importação
                'imported_by'   => Auth::id(), // Usuário que importou
            ]
        );

        // Retorna o registro se foi criado, null se já existia
        // ✅ Fix: Atualiza company_id de registros existentes que possuem NULL
        if (!$bankStatement->wasRecentlyCreated && empty($bankStatement->company_id) && $companyId) {
            $bankStatement->update(['company_id' => $companyId]);
        }

        return $bankStatement->wasRecentlyCreated ? $bankStatement : null;
    }

    /**
     * 🔄 Método para conciliar uma transação bancária com um lançamento financeiro
     */
    public function conciliarCom(TransacaoFinanceira $transacao, $valorConciliado)
    {
        \Log::info('Iniciando conciliação no modelo BankStatement', [
            'bank_statement_id' => $this->id,
            'transacao_id' => $transacao->id,
            'valor_conciliado' => $valorConciliado,
            'amount_bank_statement' => $this->amount,
            'amount_cents_bank_statement' => $this->amount_cents,
            'valor_transacao' => $transacao->valor,
            'entidade_financeira_id' => $this->entidade_financeira_id
        ]);

        try {
            // ✅ Lock pessimista para evitar race condition em conciliações simultâneas
            $locked = self::where('id', $this->id)->lockForUpdate()->first();

            if ($locked->reconciled || $locked->status_conciliacao === 'ok') {
                throw new \RuntimeException('Este lançamento já foi conciliado por outro usuário.');
            }

            // ✅ Marca o registro como conciliado
            $this->reconciled = true;

            // ✅ Define o status de conciliação com base no valor
            // Usa comparação em centavos (inteiros) para evitar erros de ponto flutuante
            // Ex: em float, 345.70 pode virar 345.6999999... causando divergências falsas
            $bankStatementCents = (int) round(abs((float) $this->amount) * 100);
            $valorConciliadoCents = (int) round((float) $valorConciliado * 100);

            if ($valorConciliadoCents === $bankStatementCents) {
                $this->status_conciliacao = 'ok'; // Conciliação perfeita (valores iguais)
                \Log::info('Status definido como: ok (conciliação perfeita)', [
                    'valor_conciliado_cents' => $valorConciliadoCents,
                    'bank_statement_cents' => $bankStatementCents,
                ]);
            } elseif ($valorConciliadoCents < $bankStatementCents) {
                $this->status_conciliacao = 'parcial'; // Conciliação parcial (valor conciliado menor)
                \Log::info('Status definido como: parcial (valor menor)', [
                    'valor_conciliado_cents' => $valorConciliadoCents,
                    'bank_statement_cents' => $bankStatementCents,
                ]);
            } else { // $valorConciliadoCents > $bankStatementCents
                $this->status_conciliacao = 'divergente'; // Conciliação divergente (valor conciliado maior)
                \Log::info('Status definido como: divergente (valor maior)', [
                    'valor_conciliado_cents' => $valorConciliadoCents,
                    'bank_statement_cents' => $bankStatementCents,
                ]);
            }

            // ✅ Salva os campos diretamente na tabela
            $this->save();

            \Log::info('BankStatement atualizado com sucesso', [
                'reconciled' => $this->reconciled,
                'status_conciliacao' => $this->status_conciliacao
            ]);

            // Saldo atualizado automaticamente pelo MovimentacaoObserver (increment/decrement O(1))

            // ✅ Salva diretamente na tabela pivot o valor conciliado e o status
            $this->transacoes()->attach($transacao->id, [
                'valor_conciliado' => $valorConciliado,
                'status_conciliacao' => $this->status_conciliacao,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            \Log::info('Relacionamento pivot criado com sucesso', [
                'bank_statement_id' => $this->id,
                'transacao_id' => $transacao->id,
                'valor_conciliado' => $valorConciliado,
                'status_conciliacao' => $this->status_conciliacao
            ]);

        } catch (\Exception $e) {
            \Log::error('Erro ao conciliar no modelo BankStatement', [
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'bank_statement_id' => $this->id,
                'transacao_id' => $transacao->id,
                'valor_conciliado' => $valorConciliado,
                'entidade_financeira_id' => $this->entidade_financeira_id
            ]);
            
            throw $e; // Re-lança a exceção para ser capturada pelo controller
        }
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
            // Remove timezone info e pega apenas os dígitos
            $dateString = preg_replace('/[^\d]/', '', substr($ofxDateString, 0, 14));
            $len = strlen($dateString);

            // Mínimo: 8 dígitos para data (YYYYMMDD)
            if ($len < 8) {
                return now()->format('Y-m-d H:i:s');
            }

            $year  = substr($dateString, 0, 4);
            $month = substr($dateString, 4, 2);
            $day   = substr($dateString, 6, 2);
            $hour  = $len >= 10 ? substr($dateString, 8, 2) : '00';
            $min   = $len >= 12 ? substr($dateString, 10, 2) : '00';
            $sec   = $len >= 14 ? substr($dateString, 12, 2) : '00';

            $dt = new \DateTime("{$year}-{$month}-{$day} {$hour}:{$min}:{$sec}");
            $dt->setTimezone(new \DateTimeZone('America/Sao_Paulo'));
            return $dt->format('Y-m-d H:i:s');
        }

        return now()->format('Y-m-d H:i:s');
    }
}
