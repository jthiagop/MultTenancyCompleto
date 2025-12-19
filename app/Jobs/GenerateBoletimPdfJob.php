<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Storage;
use Spatie\Browsershot\Browsershot;
use App\Helpers\BrowsershotHelper;
use App\Models\Financeiro\TransacaoFinanceira;
use Carbon\Carbon;

class GenerateBoletimPdfJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $timeout = 300;
    public $tries = 3;

    protected $mes;
    protected $ano;
    protected $companyId;
    protected $userId;

    public function __construct($mes, $ano, $companyId, $userId)
    {
        $this->mes = $mes;
        $this->ano = $ano;
        $this->companyId = $companyId;
        $this->userId = $userId;
    }

    public function handle()
    {
        $company = \App\Models\Company::with('addresses')->find($this->companyId);
        
        // Calcular período
        $dataInicio = Carbon::create($this->ano, $this->mes, 1)->startOfMonth();
        $dataFim = Carbon::create($this->ano, $this->mes, 1)->endOfMonth();

        // Buscar transações
        $transacoes = TransacaoFinanceira::where('company_id', $this->companyId)
            ->whereBetween('data_competencia', [$dataInicio, $dataFim])
            ->with(['lancamentoPadrao', 'entidadeFinanceira', 'costCenter'])
            ->get();

        // Calcular totais
        $totalEntradas = $transacoes->where('tipo', 'entrada')->sum('valor');
        $totalSaidas = $transacoes->where('tipo', 'saida')->sum('valor');
        $saldo = $totalEntradas - $totalSaidas;

        // Agrupar por lançamento padrão
        $entradasPorLancamento = $transacoes->where('tipo', 'entrada')
            ->groupBy('lancamento_padrao_id')
            ->map(function ($grupo) {
                return [
                    'descricao' => $grupo->first()->lancamentoPadrao->description ?? 'Sem lançamento',
                    'valor' => $grupo->sum('valor')
                ];
            });

        $saidasPorLancamento = $transacoes->where('tipo', 'saida')
            ->groupBy('lancamento_padrao_id')
            ->map(function ($grupo) {
                return [
                    'descricao' => $grupo->first()->lancamentoPadrao->description ?? 'Sem lançamento',
                    'valor' => $grupo->sum('valor')
                ];
            });

        // Gerar HTML
        $html = view('app.relatorios.financeiro.boletim_pdf', [
            'company' => $company,
            'mes' => $this->mes,
            'ano' => $this->ano,
            'dataInicio' => $dataInicio,
            'dataFim' => $dataFim,
            'totalEntradas' => $totalEntradas,
            'totalSaidas' => $totalSaidas,
            'saldo' => $saldo,
            'entradasPorLancamento' => $entradasPorLancamento,
            'saidasPorLancamento' => $saidasPorLancamento,
            'transacoes' => $transacoes,
        ])->render();

        // Gerar PDF
        $pdf = BrowsershotHelper::configureChromePath(
            Browsershot::html($html)
                ->format('A4')
                ->showBackground()
                ->margins(8, 8, 8, 8)
                ->waitUntilNetworkIdle()
        )->pdf();

        // Salvar PDF
        $filename = "boletins/boletim_{$this->mes}_{$this->ano}_" . time() . ".pdf";
        Storage::disk('public')->put($filename, $pdf);

        // Notificar usuário (opcional)
        // User::find($this->userId)->notify(new PdfGeneratedNotification($filename));

        return $filename;
    }
}
