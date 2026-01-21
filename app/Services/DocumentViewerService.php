<?php

namespace App\Services;

use App\Models\DomusDocumento;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class DocumentViewerService
{
    /**
     * Obtém a extensão do arquivo a partir do MIME type
     *
     * @param string $mimeType
     * @return string
     */
    public function getExtensionFromMimeType(string $mimeType): string
    {
        $mimeToExtension = [
            'application/pdf' => 'pdf',
            'image/png' => 'png',
            'image/jpeg' => 'jpg',
            'image/jpg' => 'jpg',
            'image/webp' => 'webp',
            'image/gif' => 'gif',
            'application/msword' => 'doc',
            'application/vnd.openxmlformats-officedocument.wordprocessingml.document' => 'docx',
            'application/vnd.ms-excel' => 'xls',
            'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet' => 'xlsx',
            'text/plain' => 'txt',
        ];

        return $mimeToExtension[$mimeType] ?? 'bin';
    }

    /**
     * Lista documentos com filtros opcionais
     *
     * @param int|null $companyId
     * @param array $filters
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function listDocuments(?int $companyId = null, array $filters = [])
    {
        $query = DomusDocumento::query();

        // Filtrar por company_id se fornecido
        if ($companyId) {
            $query->where('company_id', $companyId);
        } else {
            // Se não fornecido, usar da sessão ou do usuário autenticado
            $activeCompanyId = session('active_company_id') ?? Auth::user()?->company_id;
            if ($activeCompanyId) {
                $query->where('company_id', $activeCompanyId);
            }
        }

        // Aplicar filtros
        if (isset($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        if (isset($filters['tipo_documento'])) {
            $query->where('tipo_documento', $filters['tipo_documento']);
        }

        if (isset($filters['canal_origem'])) {
            $query->where('canal_origem', $filters['canal_origem']);
        }

        // Ordenação
        $orderBy = $filters['order_by'] ?? 'created_at';
        $orderDirection = $filters['order_direction'] ?? 'desc';
        $query->orderBy($orderBy, $orderDirection);

        return $query->get();
    }

    /**
     * Busca um documento específico
     *
     * @param int $id
     * @param int|null $companyId
     * @return DomusDocumento|null
     */
    public function getDocument(int $id, ?int $companyId = null): ?DomusDocumento
    {
        $query = DomusDocumento::where('id', $id);

        // Filtrar por company_id se fornecido
        if ($companyId) {
            $query->where('company_id', $companyId);
        } else {
            // Se não fornecido, usar da sessão ou do usuário autenticado
            $activeCompanyId = session('active_company_id') ?? Auth::user()?->company_id;
            if ($activeCompanyId) {
                $query->where('company_id', $activeCompanyId);
            }
        }

        return $query->first();
    }

    /**
     * Retorna os dados do arquivo para servir ao navegador
     *
     * @param int $id
     * @return array|null Retorna array com 'path', 'mime_type' e 'filename' ou null se não encontrado
     */
    public function getDocumentFile(int $id): ?array
    {
        $documento = $this->getDocument($id);

        if (!$documento) {
            Log::warning('Documento não encontrado para servir arquivo', ['id' => $id]);
            return null;
        }

        if (!$documento->caminho_arquivo) {
            Log::warning('Documento sem caminho de arquivo', ['id' => $id]);
            return null;
        }

        // Construir o caminho completo do arquivo
        $fullPath = Storage::disk('public')->path($documento->caminho_arquivo);

        // Verificar se o arquivo existe
        if (!file_exists($fullPath)) {
            Log::warning('Arquivo físico não encontrado', [
                'id' => $id,
                'caminho' => $documento->caminho_arquivo,
                'full_path' => $fullPath,
            ]);
            return null;
        }

        return [
            'path' => $fullPath,
            'mime_type' => $documento->mime_type ?? 'application/octet-stream',
            'filename' => $documento->nome_arquivo ?? 'documento',
        ];
    }

    /**
     * Deleta um documento (soft delete)
     *
     * @param int $id
     * @return bool
     */
    public function deleteDocument(int $id): bool
    {
        $documento = $this->getDocument($id);

        if (!$documento) {
            Log::warning('Tentativa de deletar documento inexistente', ['id' => $id]);
            return false;
        }

        try {
            // Soft delete (o modelo usa SoftDeletes)
            $documento->delete();

            Log::info('Documento deletado com sucesso', [
                'id' => $id,
                'nome_arquivo' => $documento->nome_arquivo,
            ]);

            return true;
        } catch (\Exception $e) {
            Log::error('Erro ao deletar documento', [
                'id' => $id,
                'error' => $e->getMessage(),
            ]);

            return false;
        }
    }
}


