<?php

namespace App\Helpers;

use App\Models\Financeiro\TransacaoFinanceira;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\View;

class TransacaoFormatter
{
    /**
     * Formata o botão de ações com dropdown
     */
    public function formatActions(TransacaoFinanceira $transacao, array $options = []): string
    {
        $viewAction = $options['viewAction'] ?? "abrirDrawerTransacao({$transacao->id}); return false;";
        $editRoute = $options['editRoute'] ?? route('banco.edit', $transacao->id);
        $deleteAction = $options['deleteAction'] ?? 'data-kt-transacao-table-filter="delete_row" data-transacao-id="' . $transacao->id . '"';
        $informarPagamentoAction = $options['informarPagamentoAction'] ?? "informarPagamento({$transacao->id}); return false;";
        $viewLabel = $options['viewLabel'] ?? 'Visualizar';
        $editLabel = $options['editLabel'] ?? 'Editar';
        $deleteLabel = $options['deleteLabel'] ?? 'Excluir';
        $informarPagamentoLabel = $options['informarPagamentoLabel'] ?? 'Informar pagamento';
        $showInformarPagamento = $options['showInformarPagamento'] ?? true;
        $menuWidth = $options['menuWidth'] ?? 'w-200px';

        // Converter situação para string (pode ser Enum)
        $situacaoValue = $transacao->situacao instanceof \App\Enums\SituacaoTransacao 
            ? $transacao->situacao->value 
            : $transacao->situacao;

        // Determinar se está pago/recebido
        $isPago = in_array($situacaoValue, ['pago', 'recebido']);
        
        // Verificar se está em aberto (não mostrar opção de definir como pago)
        $isEmAberto = ($situacaoValue === 'em_aberto');

        return view('app.financeiro.banco.partials.actions-button', [
            'transacao' => $transacao,
            'viewAction' => $viewAction,
            'editRoute' => $editRoute,
            'deleteAction' => $deleteAction,
            'informarPagamentoAction' => $informarPagamentoAction,
            'viewLabel' => $viewLabel,
            'editLabel' => $editLabel,
            'deleteLabel' => $deleteLabel,
            'informarPagamentoLabel' => $informarPagamentoLabel,
            'showInformarPagamento' => $showInformarPagamento,
            'menuWidth' => $menuWidth,
            'isPago' => $isPago,
            'isEmAberto' => $isEmAberto,
            'tipoTransacao' => $transacao->tipo,
        ])->render();
    }

    /**
     * Formata os anexos para exibição na tabela
     */
    public function formatAnexos(TransacaoFinanceira $transacao): string
    {
        $anexos = $transacao->modulos_anexos->take(3);
        $remainingAnexos = $transacao->modulos_anexos->count() - 3;

        $icons = [
            'pdf' => ['icon' => 'bi-file-earmark-pdf-fill', 'color' => 'text-danger'],
            'jpg' => ['icon' => 'bi-file-earmark-image-fill', 'color' => 'text-warning'],
            'jpeg' => ['icon' => 'bi-file-earmark-image-fill', 'color' => 'text-primary'],
            'png' => ['icon' => 'bi-file-earmark-image-fill', 'color' => 'text-warning'],
            'doc' => ['icon' => 'bi-file-earmark-word-fill', 'color' => 'text-info'],
            'docx' => ['icon' => 'bi-file-earmark-word-fill', 'color' => 'text-info'],
            'xls' => ['icon' => 'bi-file-earmark-spreadsheet-fill', 'color' => 'text-warning'],
            'xlsx' => ['icon' => 'bi-file-earmark-spreadsheet-fill', 'color' => 'text-warning'],
            'txt' => ['icon' => 'bi-file-earmark-text-fill', 'color' => 'text-muted'],
        ];
        $defaultIcon = ['icon' => 'bi-file-earmark-fill', 'color' => 'text-secondary'];

        return view('app.financeiro.banco.partials.anexos', [
            'transacao' => $transacao,
            'anexos' => $anexos,
            'remainingAnexos' => $remainingAnexos,
            'icons' => $icons,
            'defaultIcon' => $defaultIcon,
        ])->render();
    }
}

