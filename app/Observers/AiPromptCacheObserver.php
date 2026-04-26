<?php

namespace App\Observers;

use App\Services\Ai\DocumentExtractorService;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Session;

/**
 * Invalida o cache do system prompt da IA quando recursos que o
 * compõem (formas de pagamento, lançamentos padrão) são criados,
 * atualizados ou removidos.
 *
 * O cache é particionado por tenant + company. Aqui invalidamos a
 * chave da empresa ativa na sessão e (se o model expor company_id)
 * também a chave da empresa associada ao registro alterado. Outros
 * contextos absorvem a mudança ao expirar o TTL (5 min).
 */
class AiPromptCacheObserver
{
    public function created(Model $model): void
    {
        $this->forget($model);
    }

    public function updated(Model $model): void
    {
        $this->forget($model);
    }

    public function deleted(Model $model): void
    {
        $this->forget($model);
    }

    private function forget(Model $model): void
    {
        $companies = [];

        $modelCompany = $model->getAttribute('company_id');
        if ($modelCompany !== null) {
            $companies[] = (int) $modelCompany;
        }

        $sessionCompany = Session::has('active_company_id')
            ? (int) Session::get('active_company_id')
            : null;
        if ($sessionCompany !== null) {
            $companies[] = $sessionCompany;
        }

        // Sempre incluir invalidação genérica (sem company) para casos
        // em que o prompt foi gerado fora de um contexto de empresa.
        $companies[] = null;

        foreach (array_unique($companies, SORT_REGULAR) as $companyId) {
            (new DocumentExtractorService($companyId))->invalidatePromptCache();
        }
    }
}
