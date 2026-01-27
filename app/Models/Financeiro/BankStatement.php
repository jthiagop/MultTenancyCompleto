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
    public static function storeTransaction($account, $transaction, $entidadeId, $fileHash = null, $fileName = null)
    {
        // ✅ Usa Money::fromOfx para converter valor do OFX (pode ser negativo)
        $money = \App\Support\Money::fromOfx((float) $transaction->amount);
        $amountValue = $money->toDatabase(); // DECIMAL para precisão
        $amountCents = $money->toCents(); // Integer em centavos

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
                'company_id'    => Auth::user()->company_id,
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
                'file_hash'     => $fileHash, // Hash do arquivo (múltiplas transações do mesmo arquivo)
                'file_name'     => $fileName, // Nome do arquivo
            ]
        );

        // Retorna o registro se foi criado, null se já existia
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
            // ✅ Marca o registro como conciliado
            $this->reconciled = true;

            // ✅ Define o status de conciliação com base no valor (ambos em centavos)
            $bankStatementCentavos = abs($this->amount_cents); // Valor absoluto em centavos
            
            if ($valorConciliado == $bankStatementCentavos) {
                $this->status_conciliacao = 'ok'; // Conciliação perfeita
                \Log::info('Status definido como: ok (conciliação perfeita)');
            } elseif ($valorConciliado < $bankStatementCentavos) {
                $this->status_conciliacao = 'parcial'; // Conciliação parcial (valor menor)
                \Log::info('Status definido como: parcial (valor menor)');
            } elseif ($valorConciliado > $bankStatementCentavos) {
                $this->status_conciliacao = 'divergente'; // Conciliação divergente (valor maior)
                \Log::info('Status definido como: divergente (valor maior)');
            } else {
                $this->status_conciliacao = 'pendente'; // Valor não foi conciliado
                \Log::warning('Status definido como: pendente (valor não conciliado)');
            }

            // ✅ Salva os campos diretamente na tabela
            $this->save();

            \Log::info('BankStatement atualizado com sucesso', [
                'reconciled' => $this->reconciled,
                'status_conciliacao' => $this->status_conciliacao
            ]);

            // ✅ ATUALIZAR SALDO DA ENTIDADE (IMPORTANTE!)
            // CRÍTICO: $valorConciliado JÁ VEM EM CENTAVOS do controller
            \Log::info('🔄 Iniciando atualização de saldo', [
                'entidade_id' => $this->entidade_financeira_id,
                'valor_conciliado_centavos' => $valorConciliado,
                'bank_statement_amount' => $this->amount,
                'bank_statement_amount_cents' => $this->amount_cents,
                'tipo_transacao' => $transacao->tipo ?? 'desconhecido'
            ]);
            
            $entidade = EntidadeFinanceira::find($this->entidade_financeira_id);
            if ($entidade) {
                $saldoAntes = $entidade->saldo_atual;
                
                // IMPORTANTE: $valorConciliado já está em CENTAVOS (integer) do controller
                // DECIMAL: Usar bcmath para precisão financeira (NUNCA usar operadores aritméticos!)
                $valorEmReais = bcdiv((string) $valorConciliado, '100', 2); // Converte centavos → reais
                
                // Calcula incremento com base no tipo de transação usando bcmath
                $valorParaAdicionar = ($transacao->tipo === 'entrada') 
                    ? $valorEmReais                  // Positivo para entrada
                    : bcmul($valorEmReais, '-1', 2); // Negativo para saída (bcmath)
                
                // Atualiza saldo usando bcmath (DECIMAL precisão)
                // CRÍTICO: Converte saldo_atual para string antes de usar bcadd
                $saldoAtualStr = (string) $entidade->saldo_atual;
                $entidade->saldo_atual = bcadd($saldoAtualStr, $valorParaAdicionar, 2);
                $entidade->save();
                
                \Log::info('✅ Saldo da entidade atualizado após conciliação', [
                    'entidade_id' => $entidade->id,
                    'saldo_antes' => $saldoAntes,
                    'saldo_depois' => $entidade->saldo_atual,
                    'tipo_transacao' => $transacao->tipo,
                    'valor_conciliado_centavos' => $valorConciliado,
                    'valor_em_reais' => $valorEmReais,
                    'valor_adicionado' => $valorParaAdicionar,
                    'calculo' => "{$saldoAntes} + ({$valorParaAdicionar}) = {$entidade->saldo_atual}"
                ]);
            } else {
                \Log::error('❌ Entidade não encontrada ao atualizar saldo', [
                    'entidade_id' => $this->entidade_financeira_id
                ]);
            }

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
            $dateString = substr($ofxDateString, 0, 14);
            $dt = new \DateTime(substr($dateString, 0, 4) . '-' . substr($dateString, 4, 2) . '-' . substr($dateString, 6, 2) .
                ' ' . substr($dateString, 8, 2) . ':' . substr($dateString, 10, 2) . ':' . substr($dateString, 12, 2));
            $dt->setTimezone(new \DateTimeZone('America/Sao_Paulo'));
            return $dt->format('Y-m-d H:i:s');
        }

        return now()->format('Y-m-d H:i:s');
    }
}
