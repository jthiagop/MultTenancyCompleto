<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ConciliacaoResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'transacao_id' => $this->transacao_id,
            'descricao' => $this->descricao,
            'memo' => $this->memo,
            'tipo' => $this->tipo, // 'entrada' | 'saida'
            'valor' => (float) $this->valor,
            'status' => $this->status_conciliacao,
            'lancamento_padrao' => $this->lancamentoPadrao?->nome,
            'usuario' => $this->createdBy?->name,
            
            // Datas formatadas
            'data_conciliacao' => $this->data_conciliacao?->toDateString(),
            'data_conciliacao_formatada' => $this->data_conciliacao?->format('d/m/Y'),
            'data_extrato' => $this->data_extrato?->toDateString(),
            'data_extrato_formatada' => $this->data_extrato?->format('d/m/Y'),
            
            // Campos para detalhes (quando necessário)
            $this->mergeWhen($request->routeIs('conciliacao.detalhes'), [
                'entidade_financeira' => $this->entidade?->nome,
                'centro_custo' => $this->centroCusto?->nome,
                'arquivo_ofx' => $this->arquivoOfx?->nome_original ?? null,
                'data_importacao_ofx_formatada' => $this->arquivoOfx?->created_at?->format('d/m/Y H:i'),
                'historico_complementar' => $this->historico_complementar,
                'created_by_name' => $this->createdBy?->name,
                'created_at_formatado' => $this->created_at?->format('d/m/Y H:i'),
                'updated_by_name' => $this->updatedBy?->name,
                'updated_at_formatado' => $this->updated_at?->format('d/m/Y H:i'),
            ]),
        ];
    }
}
