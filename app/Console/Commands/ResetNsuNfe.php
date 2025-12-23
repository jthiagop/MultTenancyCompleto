<?php

namespace App\Console\Commands;

use App\Models\NotaFiscalConta;
use App\Models\Tenant;
use App\Jobs\ConsultarNotasEntradaJob;
use Illuminate\Console\Command;

class ResetNsuNfe extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'nfe:reset-nsu {cnpj? : CNPJ da conta (opcional)}
                            {--dispatch : Disparar o job imediatamente após reset}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Reseta o NSU de uma conta NFe para 0, permitindo buscar notas dos últimos 90 dias';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $cnpj = $this->argument('cnpj');
        $shouldDispatch = $this->option('dispatch');

        // Buscar todos os tenants
        $tenants = Tenant::all();
        
        if ($tenants->isEmpty()) {
            $this->error('❌ Nenhum tenant encontrado no sistema.');
            return Command::FAILURE;
        }

        $this->info("🔍 Buscando em {$tenants->count()} tenant(s)...");
        $this->newLine();

        $contasEncontradas = [];

        // Iterar por cada tenant para buscar contas
        foreach ($tenants as $tenant) {
            try {
                tenancy()->initialize($tenant);

                if ($cnpj) {
                    // Buscar conta específica
                    $cnpjLimpo = preg_replace('/[^0-9]/', '', $cnpj);
                    $conta = NotaFiscalConta::where('cnpj', $cnpjLimpo)->first();
                    
                    if ($conta) {
                        $contasEncontradas[] = [
                            'tenant' => $tenant,
                            'conta' => $conta
                        ];
                    }
                } else {
                    // Buscar todas as contas deste tenant
                    $contas = NotaFiscalConta::all();
                    foreach ($contas as $conta) {
                        $contasEncontradas[] = [
                            'tenant' => $tenant,
                            'conta' => $conta
                        ];
                    }
                }

                tenancy()->end();
            } catch (\Exception $e) {
                $this->error("Erro ao processar tenant {$tenant->id}: " . $e->getMessage());
                tenancy()->end();
                continue;
            }
        }

        if (empty($contasEncontradas)) {
            if ($cnpj) {
                $this->error("❌ Conta com CNPJ {$cnpj} não encontrada em nenhum tenant.");
            } else {
                $this->error("❌ Nenhuma conta NFe encontrada no sistema.");
            }
            return Command::FAILURE;
        }

        if ($cnpj) {
            // Reset de conta específica
            $this->resetContaEspecifica($contasEncontradas[0], $shouldDispatch);
        } else {
            // Listar e permitir seleção
            $this->resetComListagem($contasEncontradas, $shouldDispatch);
        }

        return Command::SUCCESS;
    }

    /**
     * Reseta NSU de uma conta específica
     */
    private function resetContaEspecifica(array $contaData, bool $shouldDispatch): void
    {
        $tenant = $contaData['tenant'];
        $conta = $contaData['conta'];

        $this->info("📋 Conta encontrada no Tenant: {$tenant->id}");
        $this->line("   CNPJ: {$conta->cnpj}");
        $this->line("   Razão Social: {$conta->razao_social}");
        $this->line("   NSU Atual: {$conta->ultimo_nsu}");
        $this->newLine();

        if (!$this->confirm('Deseja resetar o NSU para 0? Isso fará o sistema buscar notas dos últimos 90 dias.')) {
            $this->warn('⚠️  Operação cancelada.');
            return;
        }

        // Inicializar contexto do tenant
        tenancy()->initialize($tenant);

        $nsuAnterior = $conta->ultimo_nsu;
        $conta->ultimo_nsu = 0;
        $conta->save();

        tenancy()->end();

        $this->info("✅ NSU resetado com sucesso! ({$nsuAnterior} → 0)");
        $this->newLine();

        if ($shouldDispatch) {
            $this->info('🚀 Disparando job de consulta...');
            ConsultarNotasEntradaJob::dispatch();
            $this->info('✅ Job disparado! Acompanhe os logs para ver o progresso.');
        } else {
            $this->line('💡 Dica: Use --dispatch para disparar o job imediatamente.');
            $this->line('   Exemplo: php artisan nfe:reset-nsu ' . $conta->cnpj . ' --dispatch');
        }
    }

    /**
     * Lista todas as contas e permite seleção
     */
    private function resetComListagem(array $contasEncontradas, bool $shouldDispatch): void
    {
        $this->info('📋 Contas NFe cadastradas:');
        $this->newLine();

        $headers = ['#', 'Tenant', 'CNPJ', 'Razão Social', 'NSU Atual'];
        $rows = [];

        foreach ($contasEncontradas as $index => $data) {
            $rows[] = [
                $index + 1,
                $data['tenant']->id,
                $this->formatarCnpj($data['conta']->cnpj),
                $data['conta']->razao_social ?? 'N/A',
                $data['conta']->ultimo_nsu ?? 0,
            ];
        }

        $this->table($headers, $rows);
        $this->newLine();

        $escolha = $this->ask('Digite o número da conta para resetar (ou "all" para todas, "cancel" para cancelar)');

        if (strtolower($escolha) === 'cancel') {
            $this->warn('⚠️  Operação cancelada.');
            return;
        }

        if (strtolower($escolha) === 'all') {
            $this->resetTodasContas($contasEncontradas, $shouldDispatch);
            return;
        }

        $indice = (int) $escolha - 1;

        if (!isset($contasEncontradas[$indice])) {
            $this->error('❌ Opção inválida.');
            return;
        }

        $this->resetContaEspecifica($contasEncontradas[$indice], $shouldDispatch);
    }

    /**
     * Reseta NSU de todas as contas
     */
    private function resetTodasContas(array $contasEncontradas, bool $shouldDispatch): void
    {
        $this->warn('⚠️  ATENÇÃO: Você está prestes a resetar o NSU de TODAS as contas!');
        $this->line('   Isso fará o sistema buscar notas dos últimos 90 dias para cada uma.');
        $this->newLine();

        if (!$this->confirm('Tem certeza que deseja continuar?')) {
            $this->warn('⚠️  Operação cancelada.');
            return;
        }

        $bar = $this->output->createProgressBar(count($contasEncontradas));
        $bar->start();

        $resetadas = 0;

        foreach ($contasEncontradas as $data) {
            try {
                tenancy()->initialize($data['tenant']);
                
                $data['conta']->ultimo_nsu = 0;
                $data['conta']->save();
                
                tenancy()->end();
                
                $resetadas++;
            } catch (\Exception $e) {
                $this->error("Erro ao resetar conta {$data['conta']->cnpj}: " . $e->getMessage());
                tenancy()->end();
            }
            
            $bar->advance();
        }

        $bar->finish();
        $this->newLine(2);

        $this->info("✅ {$resetadas} conta(s) resetada(s) com sucesso!");

        if ($shouldDispatch) {
            $this->info('🚀 Disparando job de consulta...');
            ConsultarNotasEntradaJob::dispatch();
            $this->info('✅ Job disparado! Acompanhe os logs para ver o progresso.');
        }
    }

    /**
     * Formata CNPJ para exibição
     */
    private function formatarCnpj(string $cnpj): string
    {
        if (strlen($cnpj) !== 14) {
            return $cnpj;
        }

        return sprintf(
            '%s.%s.%s/%s-%s',
            substr($cnpj, 0, 2),
            substr($cnpj, 2, 3),
            substr($cnpj, 5, 3),
            substr($cnpj, 8, 4),
            substr($cnpj, 12, 2)
        );
    }
}
