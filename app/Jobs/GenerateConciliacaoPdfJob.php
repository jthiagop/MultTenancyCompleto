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

class GenerateConciliacaoPdfJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $timeout = 300;
    public $tries = 3;

    protected $filters;
    protected $companyId;
    protected $userId;

    public function __construct($filters, $companyId, $userId)
    {
        $this->filters = $filters;
        $this->companyId = $companyId;
        $this->userId = $userId;
    }

    public function handle()
    {
        $company = \App\Models\Company::find($this->companyId);
        
        // Buscar transações
        $query = TransacaoFinanceira::where('company_id', $this->companyId);
        
        if (!empty($this->filters['data_inicio'])) {
            $query->where('data_competencia', '>=', $this->filters['data_inicio']);
        }
        if (!empty($this->filters['data_fim'])) {
            $query->where('data_competencia', '<=', $this->filters['data_fim']);
        }
        if (!empty($this->filters['entidade_id'])) {
            $query->where('entidade_financeira_id', $this->filters['entidade_id']);
        }
        
        $transacoes = $query->with(['lancamentoPadrao', 'entidadeFinanceira'])->get();
        
        // Gerar HTML
        $html = view('app.relatorios.financeiro.conciliacao_pdf', [
            'transacoes' => $transacoes,
            'company' => $company,
            'filters' => $this->filters,
        ])->render();

        // Gerar PDF
        $pdf = BrowsershotHelper::configureChromePath(
            Browsershot::html($html)
                ->format('A4')
                ->landscape()
                ->showBackground()
                ->margins(8, 8, 8, 8)
        )->pdf();

        // Salvar PDF
        $filename = "conciliacoes/conciliacao_" . time() . ".pdf";
        Storage::disk('public')->put($filename, $pdf);

        // Notificar usuário (opcional - implementar notification)
        // User::find($this->userId)->notify(new PdfGeneratedNotification($filename));

        return $filename;
    }
}
