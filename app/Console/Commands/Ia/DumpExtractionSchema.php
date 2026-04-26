<?php

namespace App\Console\Commands\Ia;

use App\Services\Ai\DocumentExtractorService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

/**
 * Gera os tipos TypeScript do contrato de extração de documentos da Domus IA
 * a partir do JSON Schema canônico definido em DocumentExtractorService.
 *
 * Garante "single source of truth" entre back-end (PHP / OpenAI Structured
 * Outputs) e front-end (React / TypeScript), eliminando deriva manual.
 *
 * Uso:
 *   php artisan domus:dump-extraction-schema
 *   php artisan domus:dump-extraction-schema --check (CI: falha se gerado != commitado)
 */
class DumpExtractionSchema extends Command
{
    protected $signature = 'domus:dump-extraction-schema
        {--check : Não escreve; sai com código != 0 se o arquivo no disco estiver desatualizado}';

    protected $description = 'Gera os tipos TypeScript do schema de extração da Domus IA a partir do PHP';

    /**
     * Caminho relativo (a partir de base_path) do arquivo gerado.
     */
    private const OUTPUT_PATH = 'resources/js/react/src/pages/financeiro/ia/types/dados-extraidos.generated.ts';

    public function handle(): int
    {
        $schema = DocumentExtractorService::getResponseSchema();

        $generated = $this->renderTypeScript($schema);
        $absPath   = base_path(self::OUTPUT_PATH);

        if ($this->option('check')) {
            if (! File::exists($absPath)) {
                $this->error("Arquivo não existe: " . self::OUTPUT_PATH);
                $this->line("Rode: php artisan domus:dump-extraction-schema");
                return self::FAILURE;
            }
            $current = File::get($absPath);
            if (trim($current) !== trim($generated)) {
                $this->error("O arquivo TypeScript do schema está desatualizado.");
                $this->line("Rode: php artisan domus:dump-extraction-schema");
                return self::FAILURE;
            }
            $this->info("Schema TS está sincronizado com o PHP.");
            return self::SUCCESS;
        }

        File::ensureDirectoryExists(dirname($absPath));
        File::put($absPath, $generated);

        $this->info("Tipos TS regenerados em " . self::OUTPUT_PATH);
        return self::SUCCESS;
    }

    /**
     * Renderiza o conteúdo final do arquivo .ts.
     */
    private function renderTypeScript(array $schema): string
    {
        $body = $this->jsonSchemaToTs($schema, 0);

        $header = <<<'TS'
        /**
         * ARQUIVO GERADO AUTOMATICAMENTE — NÃO EDITE MANUALMENTE.
         *
         * Para regenerar:
         *   php artisan domus:dump-extraction-schema
         *
         * Fonte canônica:
         *   app/Services/Ai/DocumentExtractorService.php :: getResponseSchema()
         *
         * Este arquivo garante paridade entre o JSON Schema enviado à OpenAI
         * (Structured Outputs) e o tipo consumido no front-end React.
         */

        TS;

        return $header . "\nexport interface DadosExtraidos " . $body . "\n";
    }

    /**
     * Conversor recursivo de JSON Schema → TypeScript.
     *
     * Suporta:
     *  - object (com properties + required)
     *  - array (com items)
     *  - string com enum (vira união de literais)
     *  - tipos compostos como ['string', 'null'] (vira `string | null`)
     *  - integer/number → number, boolean, null
     */
    private function jsonSchemaToTs(array $schema, int $indent): string
    {
        $type = $schema['type'] ?? null;

        // Tipo composto: ['string', 'null'] → 'string | null'
        if (is_array($type)) {
            $parts = [];
            foreach ($type as $t) {
                $parts[] = $this->jsonSchemaToTs(array_merge($schema, ['type' => $t]), $indent);
            }
            return implode(' | ', array_values(array_unique($parts)));
        }

        if ($type === 'object') {
            return $this->renderObject($schema, $indent);
        }

        if ($type === 'array') {
            $items = $schema['items'] ?? ['type' => 'unknown'];
            $itemsTs = $this->jsonSchemaToTs($items, $indent);
            // Se o item é um objeto multilinha, embrulhar com Array<...> mantém legível
            return $this->isMultiline($itemsTs) ? "Array<{$itemsTs}>" : "{$itemsTs}[]";
        }

        if ($type === 'string') {
            if (! empty($schema['enum']) && is_array($schema['enum'])) {
                $literals = array_map(
                    static fn ($v) => is_string($v) ? "'" . str_replace("'", "\\'", $v) . "'" : json_encode($v),
                    $schema['enum']
                );
                return implode(' | ', $literals);
            }
            return 'string';
        }

        if ($type === 'number' || $type === 'integer') {
            return 'number';
        }

        if ($type === 'boolean') {
            return 'boolean';
        }

        if ($type === 'null') {
            return 'null';
        }

        return 'unknown';
    }

    /**
     * Renderiza um schema do tipo "object" como TypeScript.
     */
    private function renderObject(array $schema, int $indent): string
    {
        $properties = $schema['properties'] ?? [];
        if (empty($properties)) {
            return 'Record<string, unknown>';
        }

        $required = $schema['required'] ?? [];
        $pad      = str_repeat('  ', $indent);
        $padInner = str_repeat('  ', $indent + 1);

        $lines = ['{'];
        foreach ($properties as $key => $prop) {
            $optional = in_array($key, $required, true) ? '' : '?';
            $valueTs  = $this->jsonSchemaToTs($prop, $indent + 1);

            $description = $prop['description'] ?? null;
            if ($description) {
                $lines[] = $padInner . '/** ' . $this->escapeComment($description) . ' */';
            }

            $lines[] = $padInner . $this->propKey($key) . $optional . ': ' . $valueTs . ';';
        }
        $lines[] = $pad . '}';

        return implode("\n", $lines);
    }

    /**
     * Garante que chaves não-identificadoras virem strings entre aspas.
     */
    private function propKey(string $key): string
    {
        return preg_match('/^[A-Za-z_$][A-Za-z0-9_$]*$/', $key) === 1
            ? $key
            : "'" . str_replace("'", "\\'", $key) . "'";
    }

    /**
     * Sanitiza descrições para uso dentro de comentário JSDoc de uma linha.
     */
    private function escapeComment(string $text): string
    {
        $text = str_replace(["\r\n", "\n", "\r"], ' ', $text);
        return str_replace('*/', '* /', $text);
    }

    private function isMultiline(string $ts): bool
    {
        return str_contains($ts, "\n");
    }
}
