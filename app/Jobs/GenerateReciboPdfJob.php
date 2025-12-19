<?php

namespace App\Jobs;

use App\Models\Financeiro\Recibo;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Storage;
use Spatie\Browsershot\Browsershot;
use App\Helpers\BrowsershotHelper;

class GenerateReciboPdfJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $timeout = 300; // 5 minutos
    public $tries = 3;

    protected $reciboId;

    public function __construct($reciboId)
    {
        $this->reciboId = $reciboId;
    }

    public function handle()
    {
        $recibo = Recibo::with(['address', 'transacao'])->findOrFail($this->reciboId);
        $company = \App\Models\Company::find(session('active_company_id'));

        // Gerar HTML
        $html = view('app.relatorios.financeiro.recibo', [
            'recibo' => $recibo,
            'company' => $company,
            'companyLogo' => $this->logoToBase64($company),
        ])->render();

        // Gerar PDF
        $pdf = BrowsershotHelper::configureChromePath(
            Browsershot::html($html)
                ->format('A4')
                ->margins(5, 5, 5, 5)
                ->showBackground()
        )->pdf();

        // Salvar PDF no storage
        $filename = "recibos/recibo_{$recibo->id}_" . time() . ".pdf";
        Storage::disk('public')->put($filename, $pdf);

        // Atualizar recibo com caminho do PDF
        $recibo->update(['pdf_path' => $filename]);

        return $filename;
    }

    protected function logoToBase64($company): ?string
    {
        if (!$company || !$company->avatar) {
            $path = public_path('assets/media/png/perfil.svg');
        } else {
            $path = storage_path('app/public/' . $company->avatar);
        }

        if (!file_exists($path)) {
            return null;
        }

        return 'data:image/' . pathinfo($path, PATHINFO_EXTENSION) . ';base64,' . base64_encode(file_get_contents($path));
    }
}
