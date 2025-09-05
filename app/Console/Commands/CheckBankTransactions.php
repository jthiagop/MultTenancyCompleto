<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Financeiro\TransacaoFinanceira;
use Carbon\Carbon;

class CheckBankTransactions extends Command
{
    protected $signature = 'check:bank-transactions {company_id}';
    protected $description = 'Verificar transações bancárias para uma empresa específica';

    public function handle()
    {
        $companyId = $this->argument('company_id');
        
        $this->info("Verificando transações para empresa ID: {$companyId}");
        
        // Verificar transações de hoje
        $hoje = now()->format('Y-m-d');
        $this->info("Data de hoje: {$hoje}");
        
        $transacoes = TransacaoFinanceira::where('company_id', $companyId)
            ->whereDate('data_competencia', $hoje)
            ->get();
            
        $this->info("Transações de hoje: " . $transacoes->count());
        
        if ($transacoes->count() > 0) {
            foreach ($transacoes as $t) {
                $this->line("ID: {$t->id}, Tipo: {$t->tipo}, Valor: {$t->valor}, Origem: {$t->origem}, Entidade: {$t->entidade_id}");
            }
        }
        
        // Verificar transações bancárias
        $transacoesBanco = TransacaoFinanceira::where('company_id', $companyId)
            ->where(function($q) {
                $q->where('origem', 'Conciliação Bancária')
                  ->orWhere('origem', 'Banco');
            })
            ->get();
            
        $this->info("Transações bancárias: " . $transacoesBanco->count());
        
        if ($transacoesBanco->count() > 0) {
            foreach ($transacoesBanco as $t) {
                $this->line("ID: {$t->id}, Tipo: {$t->tipo}, Valor: {$t->valor}, Origem: {$t->origem}, Data: {$t->data_competencia}");
            }
        }
        
        // Verificar transações do mês atual
        $mesAtual = now()->month;
        $anoAtual = now()->year;
        
        $transacoesMes = TransacaoFinanceira::where('company_id', $companyId)
            ->where(function($q) {
                $q->where('origem', 'Conciliação Bancária')
                  ->orWhere('origem', 'Banco');
            })
            ->whereYear('data_competencia', $anoAtual)
            ->whereMonth('data_competencia', $mesAtual)
            ->get();
            
        $this->info("Transações bancárias do mês atual ({$mesAtual}/{$anoAtual}): " . $transacoesMes->count());
        
        if ($transacoesMes->count() > 0) {
            foreach ($transacoesMes as $t) {
                $this->line("ID: {$t->id}, Tipo: {$t->tipo}, Valor: {$t->valor}, Origem: {$t->origem}, Data: {$t->data_competencia}");
            }
        }
        
        return 0;
    }
}