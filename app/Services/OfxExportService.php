<?php

namespace App\Services;

use App\Enums\SituacaoTransacao;
use App\Models\EntidadeFinanceira;
use App\Models\Financeiro\TransacaoFinanceira;
use Carbon\Carbon;

class OfxExportService
{
    /**
     * Situações que devem ser excluídas da exportação OFX.
     */
    private const SITUACOES_EXCLUIDAS = [
        SituacaoTransacao::DESCONSIDERADO,
        SituacaoTransacao::PARCELADO,
    ];

    /**
     * Gera o conteúdo de um arquivo OFX (padrão 1.02 SGML) a partir das
     * transações financeiras da entidade no período.
     *
     * @param int    $entidadeId  ID da EntidadeFinanceira
     * @param string $dataInicio  Data inicial (dd/mm/YYYY)
     * @param string $dataFim     Data final   (dd/mm/YYYY)
     * @return array{conteudo: string, nome_arquivo: string, total: int}
     *
     * @throws \Exception Quando não há transações ou dados inválidos
     */
    public function gerarOfx(int $entidadeId, string $dataInicio, string $dataFim): array
    {
        // 1. Parsear datas (formato brasileiro dd/mm/YYYY)
        $dtInicio = Carbon::createFromFormat('d/m/Y', $dataInicio)->startOfDay();
        $dtFim    = Carbon::createFromFormat('d/m/Y', $dataFim)->endOfDay();

        // 2. Buscar entidade financeira e dados bancários
        $entidade = EntidadeFinanceira::forActiveCompany()
            ->with('bank')
            ->findOrFail($entidadeId);

        // 3. Buscar transações do período (mesma lógica do ExtratoController)
        $transacoes = TransacaoFinanceira::forActiveCompany()
            ->where('entidade_id', $entidadeId)
            ->whereNotIn('situacao', self::SITUACOES_EXCLUIDAS)
            ->where('agendado', false)
            ->whereBetween('data_competencia', [$dtInicio, $dtFim])
            ->with('lancamentoPadrao')
            ->orderBy('data_competencia', 'asc')
            ->orderBy('id', 'asc')
            ->get();

        if ($transacoes->isEmpty()) {
            throw new \Exception('Não há movimentações neste período para exportar.');
        }

        // 4. Dados bancários (código COMPE, agência, conta)
        $codigoBanco = $entidade->bank?->compe_code ?? '999';
        $agencia     = $entidade->agencia ?? '0001';
        $conta       = $entidade->conta ?? (string) $entidade->id;
        $tipoConta   = $this->mapearTipoConta($entidade->account_type);

        // 5. Timestamps formatados para OFX (YYYYMMDDHHMMSS)
        $agora      = Carbon::now()->format('YmdHis');
        $dtInicioFmt = $dtInicio->format('YmdHis');
        $dtFimFmt    = $dtFim->format('YmdHis');

        // 6. Calcular saldo final (para LEDGERBAL)
        $saldoInicial = (float) ($entidade->saldo_inicial ?? 0);
        $saldoFinal = $this->calcularSaldoFinal($entidade, $dtFim);

        // 7. Montar o arquivo OFX
        $ofx = $this->montarCabecalho();
        $ofx .= $this->montarSignOn($agora);
        $ofx .= $this->montarBankMsgsInicio($codigoBanco, $agencia, $conta, $tipoConta, $dtInicioFmt, $dtFimFmt);

        // 8. Loop de transações
        foreach ($transacoes as $transacao) {
            $ofx .= $this->montarTransacao($transacao);
        }

        // 9. Fechamento
        $ofx .= $this->montarBankMsgsFim($saldoFinal, $dtFimFmt);

        // 10. Nome do arquivo
        $nomeEntidade = $this->limparTexto($entidade->nome);
        $periodo = $dtInicio->format('Ymd') . '_' . $dtFim->format('Ymd');
        $nomeArquivo = "Extrato_{$nomeEntidade}_{$periodo}.ofx";

        return [
            'conteudo'     => $ofx,
            'nome_arquivo' => $nomeArquivo,
            'total'        => $transacoes->count(),
        ];
    }

    /**
     * Monta o cabeçalho obrigatório do OFX 1.02 (SGML).
     */
    private function montarCabecalho(): string
    {
        return "OFXHEADER:100\n"
            . "DATA:OFXSGML\n"
            . "VERSION:102\n"
            . "SECURITY:NONE\n"
            . "ENCODING:USASCII\n"
            . "CHARSET:1252\n"
            . "COMPRESSION:NONE\n"
            . "OLDFILEUID:NONE\n"
            . "NEWFILEUID:NONE\n\n";
    }

    /**
     * Monta o bloco SIGNONMSGSRSV1.
     */
    private function montarSignOn(string $dtServer): string
    {
        return "<OFX>\n"
            . "  <SIGNONMSGSRSV1>\n"
            . "    <SONRS>\n"
            . "      <STATUS><CODE>0<SEVERITY>INFO</STATUS>\n"
            . "      <DTSERVER>{$dtServer}\n"
            . "      <LANGUAGE>POR\n"
            . "    </SONRS>\n"
            . "  </SIGNONMSGSRSV1>\n";
    }

    /**
     * Monta o início do bloco BANKMSGSRSV1 com dados da conta.
     */
    private function montarBankMsgsInicio(string $banco, string $agencia, string $conta, string $tipoConta, string $dtInicio, string $dtFim): string
    {
        return "  <BANKMSGSRSV1>\n"
            . "    <STMTTRNRS>\n"
            . "      <TRNUID>1001\n"
            . "      <STATUS><CODE>0<SEVERITY>INFO</STATUS>\n"
            . "      <STMTRS>\n"
            . "        <CURDEF>BRL\n"
            . "        <BANKACCTFROM>\n"
            . "          <BANKID>{$banco}\n"
            . "          <BRANCHID>{$agencia}\n"
            . "          <ACCTID>{$conta}\n"
            . "          <ACCTTYPE>{$tipoConta}\n"
            . "        </BANKACCTFROM>\n"
            . "        <BANKTRANLIST>\n"
            . "          <DTSTART>{$dtInicio}\n"
            . "          <DTEND>{$dtFim}\n";
    }

    /**
     * Monta um bloco STMTTRN para uma transação.
     */
    private function montarTransacao(TransacaoFinanceira $transacao): string
    {
        $dataMov = Carbon::parse($transacao->data_competencia)->format('YmdHis');

        // Sinal do valor: entradas positivas, saídas negativas
        $valor = (float) $transacao->valor;
        $tipo  = 'OTHER';

        if ($transacao->tipo === 'entrada') {
            $tipo = 'CREDIT';
            // Valor já é positivo
        } else {
            $tipo = 'DEBIT';
            $valor = $valor * -1; // Saídas devem ser negativas no OFX
        }

        $valorFormatado = number_format($valor, 2, '.', '');

        // Histórico/Memo: descrição + categoria do lançamento padrão
        $descricao = $transacao->descricao ?? '';
        $categoria = $transacao->lancamentoPadrao?->description ?? '';
        $memo = trim("{$descricao} - {$categoria}", ' -');
        $memo = $this->limparTexto($memo ?: 'LANCAMENTO');

        // ID único da transação (FITID) — prefixo DOMUS + ID
        $fitId = "DOMUS-{$transacao->id}";

        return "          <STMTTRN>\n"
            . "            <TRNTYPE>{$tipo}\n"
            . "            <DTPOSTED>{$dataMov}\n"
            . "            <TRNAMT>{$valorFormatado}\n"
            . "            <FITID>{$fitId}\n"
            . "            <MEMO>{$memo}\n"
            . "          </STMTTRN>\n";
    }

    /**
     * Monta o fechamento do BANKMSGSRSV1 com saldo.
     */
    private function montarBankMsgsFim(float $saldo, string $dtFim): string
    {
        $saldoFormatado = number_format($saldo, 2, '.', '');

        return "        </BANKTRANLIST>\n"
            . "        <LEDGERBAL>\n"
            . "          <BALAMT>{$saldoFormatado}\n"
            . "          <DTASOF>{$dtFim}\n"
            . "        </LEDGERBAL>\n"
            . "      </STMTRS>\n"
            . "    </STMTTRNRS>\n"
            . "  </BANKMSGSRSV1>\n"
            . "</OFX>\n";
    }

    /**
     * Calcula o saldo final da entidade até a data informada.
     * Usa a mesma lógica do ExtratoController.
     */
    private function calcularSaldoFinal(EntidadeFinanceira $entidade, Carbon $dataFim): float
    {
        $saldoInicial = (float) ($entidade->saldo_inicial ?? 0);

        $movimentos = TransacaoFinanceira::forActiveCompany()
            ->where('entidade_id', $entidade->id)
            ->whereNotIn('situacao', self::SITUACOES_EXCLUIDAS)
            ->where('agendado', false)
            ->where('data_competencia', '<=', $dataFim)
            ->selectRaw("
                SUM(CASE WHEN tipo = 'entrada' THEN valor ELSE 0 END) as total_entradas,
                SUM(CASE WHEN tipo = 'saida' THEN valor ELSE 0 END) as total_saidas
            ")
            ->first();

        $entradas = (float) ($movimentos->total_entradas ?? 0);
        $saidas   = (float) ($movimentos->total_saidas ?? 0);

        return round($saldoInicial + $entradas - $saidas, 2);
    }

    /**
     * Mapeia o tipo de conta do sistema para o padrão OFX.
     */
    private function mapearTipoConta(?string $accountType): string
    {
        return match (strtolower($accountType ?? 'checking')) {
            'checking', 'conta_corrente', 'corrente' => 'CHECKING',
            'savings', 'poupanca', 'poupança'        => 'SAVINGS',
            'credit', 'credito', 'crédito'           => 'CREDITLINE',
            default                                   => 'CHECKING',
        };
    }

    /**
     * Limpa texto sensível para o formato OFX/SGML.
     * Remove acentos, caracteres especiais e limita o comprimento.
     */
    private function limparTexto(string $texto): string
    {
        // Transliterar UTF-8 para ASCII
        $texto = @iconv('UTF-8', 'ASCII//TRANSLIT//IGNORE', $texto) ?: $texto;

        // Remover caracteres que quebram o SGML (mantém alfanuméricos, espaços e hífens)
        $texto = preg_replace('/[^a-zA-Z0-9\s\-\/]/', '', $texto);

        // Uppercase e limitar tamanho
        return strtoupper(substr(trim($texto), 0, 255));
    }
}
