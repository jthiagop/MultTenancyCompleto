<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        // Se a coluna de origem não existe mais (migração já executada
        // anteriormente ou tenant novo), não há o que migrar.
        if (! Schema::hasColumn('lancamento_padraos', 'company_id')) {
            return;
        }

        $now = now();

        DB::table('lancamento_padraos')
            ->whereNotNull('company_id')
            ->select('id', 'company_id')
            ->orderBy('id')
            ->chunk(500, function ($rows) use ($now) {
                $payload = [];

                foreach ($rows as $row) {
                    $payload[] = [
                        'lancamento_padrao_id' => (int) $row->id,
                        'company_id'           => (int) $row->company_id,
                        'created_at'           => $now,
                        'updated_at'           => $now,
                    ];
                }

                if (! empty($payload)) {
                    DB::table('lancamento_padrao_company')->insertOrIgnore($payload);
                }
            });
    }

    public function down(): void
    {
        // Não repopula a coluna antiga — o down da migração seguinte
        // (drop_company_id_from_lancamento_padraos_table) é o caminho de
        // rollback coordenado. Aqui esvaziamos o pivot para começar limpo
        // caso o operador decida reverter toda a cadeia.
        DB::table('lancamento_padrao_company')->truncate();
    }
};
