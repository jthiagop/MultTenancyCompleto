<?php

namespace App\Console\Commands;

use App\Models\Tenant;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

/**
 * Detecta tenants órfãos no banco central — registros sem nenhum domínio
 * vinculado. Esses tenants são tipicamente resíduo de uma criação que
 * falhou no meio do processo (ex.: pipeline de migração quebrou e o
 * controller não chegou a registrar o domínio).
 *
 * Sem domínio, o tenant é inacessível por HTTP mas continua ocupando o
 * banco MySQL `tenant{uuid}` — desperdício de espaço e fonte de confusão
 * em backups e auditorias.
 *
 * Modo padrão (sem flags): apenas LISTA. Use `--force` para deletar.
 */
class CleanupOrphanTenants extends Command
{
    protected $signature = 'tenants:cleanup-orphans
        {--force : Deleta de fato (caso contrário, apenas lista)}
        {--id= : Limita a operação a um único tenant id (UUID)}';

    protected $description = 'Detecta e (opcionalmente) remove tenants sem domínio vinculado';

    public function handle(): int
    {
        $query = Tenant::query()
            ->doesntHave('domains');

        if ($id = $this->option('id')) {
            $query->where('id', $id);
        }

        $orphans = $query->get();

        if ($orphans->isEmpty()) {
            $this->info('✓ Nenhum tenant órfão encontrado.');
            return self::SUCCESS;
        }

        $this->warn(sprintf('Encontrado(s) %d tenant(s) órfão(s):', $orphans->count()));
        $this->table(
            ['ID', 'Nome', 'E-mail', 'Criado em'],
            $orphans->map(fn (Tenant $t) => [
                $t->id,
                $t->name ?? '—',
                $t->email ?? '—',
                $t->created_at?->format('d/m/Y H:i') ?? '—',
            ])->all(),
        );

        if (! $this->option('force')) {
            $this->newLine();
            $this->comment('Modo dry-run: nenhum tenant foi removido.');
            $this->comment('Use --force para confirmar a remoção (deleta tenant + DB físico).');
            return self::SUCCESS;
        }

        if (! $this->confirm('Confirmar remoção definitiva destes tenants e seus bancos?', false)) {
            $this->info('Operação cancelada pelo usuário.');
            return self::SUCCESS;
        }

        $deleted = 0;
        $failures = 0;

        foreach ($orphans as $tenant) {
            try {
                // $tenant->delete() dispara Jobs\DeleteDatabase via TenantDeleted,
                // o que remove o DB físico do MySQL. Se algo falhar aqui (ex.:
                // o DB já foi removido manualmente), ainda removemos o registro
                // central via DB::table direto como fallback.
                $tenant->delete();
                $deleted++;
                $this->line("  ✓ Removido: {$tenant->id}");
            } catch (\Throwable $e) {
                // Fallback: remove só o registro central, ignora a tentativa
                // de drop do DB (que pode estar em estado inconsistente).
                try {
                    DB::table('tenants')->where('id', $tenant->id)->delete();
                    $deleted++;
                    $this->warn("  ⚠ Removido apenas do banco central (drop do DB falhou): {$tenant->id}");
                    $this->line('    motivo: ' . $e->getMessage());
                } catch (\Throwable $fallbackError) {
                    $failures++;
                    $this->error("  ✗ Falha total ao remover {$tenant->id}: " . $fallbackError->getMessage());
                }
            }
        }

        $this->newLine();
        $this->info("Resumo: {$deleted} removido(s), {$failures} falha(s).");

        return $failures > 0 ? self::FAILURE : self::SUCCESS;
    }
}
