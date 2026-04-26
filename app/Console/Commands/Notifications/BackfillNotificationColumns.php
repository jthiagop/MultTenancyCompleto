<?php

namespace App\Console\Commands\Notifications;

use App\Models\Tenant;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

/**
 * Popula as colunas físicas (company_id, title, message, channel, meta, sent_at)
 * a partir do payload `data` para todos os registros pré-existentes na tabela
 * `notifications` de cada tenant.
 *
 * Idempotente: registros já preenchidos são ignorados (WHERE coluna IS NULL).
 *
 * Uso:
 *   php artisan notifications:backfill-columns
 *   php artisan notifications:backfill-columns --tenant=<uuid>
 *   php artisan notifications:backfill-columns --chunk=500 --dry-run
 */
class BackfillNotificationColumns extends Command
{
    protected $signature = 'notifications:backfill-columns
        {--tenant= : Limita a execução a um tenant específico (UUID)}
        {--chunk=500 : Tamanho do batch a processar por iteração}
        {--dry-run : Apenas conta e exibe sem aplicar updates}';

    protected $description = 'Popula colunas físicas em notifications a partir do payload data legado';

    public function handle(): int
    {
        $tenantFilter = $this->option('tenant');
        $chunk        = max(50, (int) $this->option('chunk'));
        $dryRun       = (bool) $this->option('dry-run');

        $tenants = $tenantFilter
            ? Tenant::where('id', $tenantFilter)->get()
            : Tenant::all();

        if ($tenants->isEmpty()) {
            $this->warn('Nenhum tenant elegível.');
            return self::SUCCESS;
        }

        $this->info(sprintf(
            'Backfill em %d tenant(s)%s — chunk=%d',
            $tenants->count(),
            $dryRun ? ' [DRY RUN]' : '',
            $chunk
        ));

        $totalGeral = 0;

        foreach ($tenants as $tenant) {
            $tenant->run(function () use ($tenant, $chunk, $dryRun, &$totalGeral) {
                $totalTenant = 0;

                DB::table('notifications')
                    ->whereNull('title')
                    ->orWhereNull('message')
                    ->orWhereNull('channel')
                    ->orWhereNull('meta')
                    ->orderBy('id')
                    ->chunkById($chunk, function ($rows) use (&$totalTenant, $dryRun) {
                        foreach ($rows as $row) {
                            $data = $this->decodeData($row->data);
                            if (! is_array($data)) {
                                continue;
                            }

                            $patch = [
                                'company_id' => $row->company_id ?? ($data['company_id'] ?? null),
                                'title'      => $row->title      ?? mb_substr((string) ($data['title'] ?? ''), 0, 191),
                                'message'    => $row->message    ?? (string) ($data['message'] ?? ''),
                                'channel'    => $row->channel    ?? 'app',
                                'meta'       => $row->meta       ?? json_encode(
                                    collect($data)->except(['title', 'message', 'company_id'])->all(),
                                    JSON_UNESCAPED_UNICODE
                                ),
                                'sent_at'    => $row->sent_at    ?? $row->created_at,
                            ];

                            // Trim de strings vazias para NULL (consistência).
                            foreach (['title', 'message'] as $col) {
                                if ($patch[$col] === '') {
                                    $patch[$col] = null;
                                }
                            }

                            if (! $dryRun) {
                                DB::table('notifications')
                                    ->where('id', $row->id)
                                    ->update($patch);
                            }

                            $totalTenant++;
                        }
                    }, 'id', 'id');

                $totalGeral += $totalTenant;
                $this->line(sprintf('  Tenant %s: %d registro(s)', $tenant->id, $totalTenant));
            });
        }

        $this->newLine();
        $this->info(sprintf('Total processado: %d', $totalGeral));

        Log::info('[BackfillNotificationColumns] Execução concluída', [
            'tenants' => $tenants->count(),
            'rows'    => $totalGeral,
            'dry_run' => $dryRun,
        ]);

        return self::SUCCESS;
    }

    private function decodeData(mixed $raw): array|string|null
    {
        if (is_array($raw)) {
            return $raw;
        }

        if (! is_string($raw) || $raw === '') {
            return null;
        }

        $decoded = json_decode($raw, true);
        return is_array($decoded) ? $decoded : null;
    }
}
