<?php

namespace App\Services;

use App\Models\Movimentacao;
use App\Models\EntidadeFinanceira;
use Carbon\Carbon;

class LoteContabilExportService
{
    /**
     * Gera o conteúdo de um arquivo de Lote Contábil (TXT ou CSV)
     * no formato delimitado por ponto-e-vírgula para importação no Alterdata WCont.
     *
     * Layout: DATA;CONTA_DEBITO;CONTA_CREDITO;VALOR;HISTORICO;DOCUMENTO
     *
     * @param int    $entidadeId  ID da EntidadeFinanceira
     * @param string $dataInicio  Data inicial (dd/mm/YYYY)
     * @param string $dataFim     Data final   (dd/mm/YYYY)
     * @param string $campoData   Campo de data a usar ('data' ou 'data_competencia')
     * @param string $formato     Formato de saída ('txt' ou 'csv')
     * @return array{conteudo: string, nome_arquivo: string, total: int, ignoradas: int}
     *
     * @throws \Exception Quando não há movimentações ou dados inválidos
     */
    public function gerar(
        int $entidadeId,
        string $dataInicio,
        string $dataFim,
        string $campoData = 'data',
        string $formato = 'txt'
    ): array {
        // 1. Parsear datas (formato brasileiro dd/mm/YYYY)
        $dtInicio = Carbon::createFromFormat('d/m/Y', $dataInicio)->startOfDay();
        $dtFim    = Carbon::createFromFormat('d/m/Y', $dataFim)->endOfDay();

        // 2. Buscar entidade financeira
        $entidade = EntidadeFinanceira::forActiveCompany()
            ->findOrFail($entidadeId);

        // 3. Buscar movimentações do período com contas contábeis
        $movimentacoes = Movimentacao::where('company_id', session('active_company_id'))
            ->where('entidade_id', $entidadeId)
            ->whereBetween($campoData, [$dtInicio, $dtFim])
            ->with([
                'contaDebito',
                'contaCredito',
                'lancamentoPadrao.contaDebito',
                'lancamentoPadrao.contaCredito',
                'origem',
            ])
            ->orderBy($campoData, 'asc')
            ->orderBy('id', 'asc')
            ->get();

        if ($movimentacoes->isEmpty()) {
            throw new \Exception('Não há movimentações neste período para exportar.');
        }

        // 4. Montar linhas do arquivo
        $linhas   = [];
        $ignoradas = 0;

        foreach ($movimentacoes as $mov) {
            // Resolver contas contábeis (fallback para lançamento padrão)
            $contaDebito  = $mov->contaDebito ?? $mov->lancamentoPadrao?->contaDebito;
            $contaCredito = $mov->contaCredito ?? $mov->lancamentoPadrao?->contaCredito;

            // Pegar o código externo (Código Reduzido do Alterdata)
            $codDebito  = $contaDebito?->external_code;
            $codCredito = $contaCredito?->external_code;

            // Se não tem código externo mapeado, ignorar
            if (!$codDebito || !$codCredito) {
                $ignoradas++;
                continue;
            }

            // Formatação dos campos
            $data = Carbon::parse($mov->{$campoData})->format('d/m/Y');

            // Valor sem separador de milhar, ponto como decimal (padrão Alterdata)
            $valor = number_format((float) $mov->valor, 2, '.', '');

            // Histórico: limpar ponto-e-vírgula para não quebrar o layout
            $historico = str_replace(';', ',', $mov->descricao ?? '');
            $historico = mb_strimwidth($historico, 0, 200); // limitar tamanho

            // Número do documento (via origem polimórfica, se for TransacaoFinanceira)
            $documento = '';
            if ($mov->origem && method_exists($mov->origem, 'getAttribute')) {
                $documento = str_replace(';', ',', $mov->origem->numero_documento ?? '');
            }

            // Montar linha: DATA;CONTA_DEBITO;CONTA_CREDITO;VALOR;HISTORICO;DOCUMENTO
            $linhas[] = "{$data};{$codDebito};{$codCredito};{$valor};{$historico};{$documento}";
        }

        if (empty($linhas)) {
            throw new \Exception(
                'Nenhuma movimentação possui contas contábeis com código externo (Alterdata) configurado. ' .
                'Verifique o cadastro do Plano de Contas e do Lançamento Padrão.'
            );
        }

        // 5. Montar conteúdo final
        $cabecalho = '';
        if ($formato === 'csv') {
            $cabecalho = "DATA;CONTA_DEBITO;CONTA_CREDITO;VALOR;HISTORICO;DOCUMENTO\r\n";
        }

        // Quebra de linha Windows (\r\n) para compatibilidade com sistemas desktop
        $conteudo = $cabecalho . implode("\r\n", $linhas);

        // 6. Nome do arquivo
        $nomeEntidade = preg_replace('/[^a-zA-Z0-9_-]/', '_', $entidade->nome ?? 'entidade');
        $periodoStr   = $dtInicio->format('Ymd') . '_' . $dtFim->format('Ymd');
        $extensao     = $formato === 'csv' ? 'csv' : 'txt';
        $nomeArquivo  = "lote_contabil_{$nomeEntidade}_{$periodoStr}.{$extensao}";

        return [
            'conteudo'     => $conteudo,
            'nome_arquivo' => $nomeArquivo,
            'total'        => count($linhas),
            'ignoradas'    => $ignoradas,
        ];
    }
}
