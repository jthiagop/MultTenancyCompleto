<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Remove a constraint UNIQUE incorreta de `file_hash` (se existir) e
     * adiciona a chave composta `(fitid, dtposted, entidade_financeira_id)`
     * que é o critério real de unicidade de transação OFX.
     *
     * Nota técnica: a versão anterior usava try/catch ao redor de
     * `$table->dropUnique(...)` dentro do callback do `Schema::table`. Isso
     * é inócuo: o callback apenas registra o comando no Blueprint; a query
     * SQL real (`alter table ... drop index ...`) só é executada DEPOIS,
     * em `Blueprint->build()`, fora do try/catch. Resultado: a exceção
     * escapava, derrubava `tenants:migrate` durante o evento `TenantCreated`
     * e o controller `TenantController@store` nunca chegava a criar o
     * domínio do novo tenant. Esta versão verifica a existência do índice
     * com `SHOW INDEX` antes de tentar removê-lo, e idem para a inclusão
     * do índice composto — tornando a migration idempotente e segura.
     */
    public function up(): void
    {
        if (! Schema::hasTable('bank_statements')) {
            return;
        }

        if ($this->indexExists('bank_statements', 'bank_statements_file_hash_unique')) {
            Schema::table('bank_statements', function (Blueprint $table) {
                $table->dropUnique('bank_statements_file_hash_unique');
            });
        }

        if (! $this->indexExists('bank_statements', 'unique_ofx_transaction_v2')) {
            // Garante que as colunas alvo existem antes de criar o índice
            // composto — protege migrations rodadas em DBs muito antigos.
            $hasAllColumns = Schema::hasColumn('bank_statements', 'fitid')
                && Schema::hasColumn('bank_statements', 'dtposted')
                && Schema::hasColumn('bank_statements', 'entidade_financeira_id');

            if ($hasAllColumns) {
                Schema::table('bank_statements', function (Blueprint $table) {
                    $table->unique(
                        ['fitid', 'dtposted', 'entidade_financeira_id'],
                        'unique_ofx_transaction_v2',
                    );
                });
            }
        }
    }

    public function down(): void
    {
        if (! Schema::hasTable('bank_statements')) {
            return;
        }

        if ($this->indexExists('bank_statements', 'unique_ofx_transaction_v2')) {
            Schema::table('bank_statements', function (Blueprint $table) {
                $table->dropUnique('unique_ofx_transaction_v2');
            });
        }

        if (! $this->indexExists('bank_statements', 'bank_statements_file_hash_unique')
            && Schema::hasColumn('bank_statements', 'file_hash')) {
            Schema::table('bank_statements', function (Blueprint $table) {
                $table->unique('file_hash');
            });
        }
    }

    /**
     * Verifica em SQL bruto se um índice nomeado existe na tabela.
     * Mais confiável que tentar `dropUnique` em try/catch (que não captura
     * o erro porque a SQL é executada depois do callback).
     */
    private function indexExists(string $table, string $indexName): bool
    {
        $database = DB::connection()->getDatabaseName();

        $rows = DB::select(
            'SELECT 1 FROM INFORMATION_SCHEMA.STATISTICS
             WHERE TABLE_SCHEMA = ? AND TABLE_NAME = ? AND INDEX_NAME = ?
             LIMIT 1',
            [$database, $table, $indexName],
        );

        return ! empty($rows);
    }
};
