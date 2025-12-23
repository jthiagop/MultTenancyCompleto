<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Models\NotaFiscalConta;
use App\Models\DfeDocument;
use App\Models\DfeEvent;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use NFePHP\NFe\Tools;
use NFePHP\Common\Certificate;

use App\Models\Tenant;

class ConsultarNotasEntradaJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function handle()
    {
        // 1. Busca todos os tenants
        $tenants = Tenant::all();
        Log::info("ConsultarNotasEntradaJob: Iniciando processamento para " . $tenants->count() . " tenants.");

        foreach ($tenants as $tenant) {
            try {
                // Inicializa o contexto do tenant
                tenancy()->initialize($tenant);

                // Busca todas as configs ativas
                $contas = NotaFiscalConta::where('ativo', true)->with(['company', 'company.addresses'])->get();
                Log::info("Tenant {$tenant->id}: Encontradas " . $contas->count() . " contas ativas.");

                foreach ($contas as $conta) {
                    $this->processarConta($conta);
                }

                // Finaliza o contexto do tenant
                tenancy()->end();
            } catch (\Exception $e) {
                Log::error("Erro ao processar tenant {$tenant->id}: " . $e->getMessage());
                continue;
            }
        }
    }

    private function processarConta($conta)
    {
        try {
            Log::info("Processando conta CNPJ: {$conta->cnpj}");

            // 2. Setup do Certificado
            // Nota: Mantendo 'public' disk pois é onde o upload salva
            if (!Storage::disk('public')->exists($conta->certificado_path)) {
                Log::warning("Certificado não encontrado: {$conta->certificado_path}");
                return;
            }
            
            $pfxContent = Storage::disk('public')->get($conta->certificado_path);
            $senha = Crypt::decryptString($conta->certificado_senha);
            $certificate = Certificate::readPfx($pfxContent, $senha);

            $uf = $conta->company->uf ?? $conta->company->addresses->uf ?? 'PE';
            Log::info("Configurando Tools para {$conta->cnpj} (UF: {$uf}, Amb: " . ($conta->ambiente ?? 1) . ")");

            $tools = new Tools(json_encode([
                "atualizacao" => date('Y-m-d h:i:s'),
                "tpAmb" => $conta->ambiente ?? 1, // 1 = Produção
                "razaosocial" => $conta->company->name,
                "siglaUF" => $uf,
                "cnpj" => $conta->cnpj,
                "schemes" => "PL_009_V4",
                "versao" => "4.00",
            ]), $certificate);
            
            $tools->model('55');

            // 3. Consulta em Loop (Lotes de 50)
            $ultimoNSU = $conta->ultimo_nsu;
            $modoHistorico = ($ultimoNSU == 0);
            
            if ($modoHistorico) {
                Log::info("🕒 MODO HISTÓRICO ATIVADO - Buscando últimos 90 dias a partir do NSU 0");
            } else {
                Log::info("Iniciando consulta DistDFe a partir do NSU: {$ultimoNSU}");
            }
            
            // Define limite de loops baseado no modo
            $maxLoops = $modoHistorico ? 100 : 5; // Histórico: até 100 lotes (5000 docs), Normal: 5 lotes (250 docs)
            $loteAtual = 0;
            $totalDocumentos = 0;
            
            for ($i = 0; $i < $maxLoops; $i++) {
                $loteAtual++;
                
                $resp = $tools->sefazDistDFe($ultimoNSU);
                
                $dom = new \DOMDocument();
                $dom->loadXML($resp);
                
                $cStatNode = $dom->getElementsByTagName('cStat')->item(0);
                if (!$cStatNode) break;

                $cStat = $cStatNode->nodeValue;
                $xMotivo = $dom->getElementsByTagName('xMotivo')->item(0)->nodeValue ?? 'Sem motivo';
                
                if ($modoHistorico) {
                    Log::info("📦 Lote {$loteAtual}/{$maxLoops}: cStat={$cStat} - {$xMotivo}");
                } else {
                    Log::info("Retorno SEFAZ (Loop {$i}): cStat={$cStat} - {$xMotivo}");
                }

                // Atualiza o ponteiro NSU se houver sucesso na comunicação
                if (in_array($cStat, ['138', '137'])) {
                    $maxNSUNode = $dom->getElementsByTagName('maxNSU')->item(0);
                    if ($maxNSUNode) {
                        $novoMaxNSU = $maxNSUNode->nodeValue;
                        if ($novoMaxNSU > $ultimoNSU) {
                            $conta->update(['ultimo_nsu' => $novoMaxNSU]);
                            $ultimoNSU = $novoMaxNSU;
                            
                            if ($modoHistorico) {
                                Log::info("   ✓ NSU atualizado: {$ultimoNSU}");
                            } else {
                                Log::info("NSU atualizado para: {$ultimoNSU}");
                            }
                        }
                    }
                }

                if ($cStat == '137') {
                    if ($modoHistorico) {
                        Log::info("✅ MODO HISTÓRICO CONCLUÍDO - Todos os documentos disponíveis foram baixados");
                        Log::info("   Total de lotes processados: {$loteAtual}");
                        Log::info("   Total de documentos: {$totalDocumentos}");
                        Log::info("   NSU final: {$ultimoNSU}");
                    } else {
                        Log::info("Nenhum documento novo encontrado.");
                    }
                    break; // Nada novo encontrado
                }

                $docs = $dom->getElementsByTagName('docZip');
                if ($docs->length == 0) break;

                $docsNesteLote = $docs->length;
                $totalDocumentos += $docsNesteLote;
                
                if ($modoHistorico) {
                    Log::info("   📄 {$docsNesteLote} documentos neste lote (Total: {$totalDocumentos})");
                } else {
                    Log::info("Encontrados " . $docs->length . " documentos zipados.");
                }

                foreach ($docs as $doc) {
                    $nsuItem = $doc->getAttribute('NSU');
                    $schema = $doc->getAttribute('schema');
                    $xmlContent = gzdecode(base64_decode($doc->nodeValue));

                    $this->processarXml($conta, $nsuItem, $schema, $xmlContent);
                }
                
                // Em modo histórico, se atingiu o limite de loops, avisar
                if ($modoHistorico && $i == ($maxLoops - 1)) {
                    Log::warning("⚠️  Atingido limite de {$maxLoops} lotes. Pode haver mais documentos disponíveis.");
                    Log::warning("   Execute o job novamente para continuar de onde parou (NSU: {$ultimoNSU})");
                }
            }

        } catch (\Exception $e) {
            Log::error("Erro DFe {$conta->cnpj}: " . $e->getMessage());
        }
    }

    private function processarXml($conta, $nsu, $schema, $xml)
    {
        // Usa Transaction para garantir integridade (Banco + Arquivo)
        DB::transaction(function () use ($conta, $nsu, $schema, $xml) {
            
            $xmlObj = simplexml_load_string($xml);
            $pathStorage = "tenants/{$conta->company_id}/dfe/xmls"; // Seu caminho personalizado
            
            // --- CENÁRIO 1: É UMA NOTA (Resumo ou Completa) ---
            if (strpos($schema, 'resNFe') !== false || strpos($schema, 'procNFe') !== false) {
                
                $dados = $this->extrairDadosNFe($schema, $xmlObj);
                $nomeArquivo = "{$dados['chave']}-{$nsu}.xml";
                
                // Salva no Disco (usando local para XMLs, mais seguro)
                Storage::disk('local')->put("{$pathStorage}/{$nomeArquivo}", $xml);

                // Insere ou Atualiza na tabela dfe_documents
                DB::table('dfe_documents')->updateOrInsert(
                    ['company_id' => $conta->company_id, 'chave_acesso' => $dados['chave']],
                    [
                        'nsu' => $nsu, // Atualiza para o NSU mais recente (ex: se baixou o completo depois do resumo)
                        'schema_xml' => $schema,
                        'modelo' => 55,
                        'tp_amb' => 1,
                        'emitente_nome' => $dados['nome'],
                        'emitente_cnpj' => $dados['cnpj'],
                        'data_emissao' => $dados['emissao'],
                        'valor_total' => $dados['valor'],
                        'status_sistema' => $dados['completo'] ? 'downloaded' : 'novo',
                        'xml_completo' => $dados['completo'],
                        'xml_path' => "{$pathStorage}/{$nomeArquivo}",
                        'xml_hash' => hash('sha256', $xml),
                        'updated_at' => now(),
                    ] + ($dados['completo'] ? [] : ['created_at' => now()]) // Só define created_at se for insert
                );
            }
            
            // --- CENÁRIO 2: É UM EVENTO (Cancelamento, Carta de Correção, Ciência) ---
            elseif (strpos($schema, 'resEvento') !== false || strpos($schema, 'procEventoNFe') !== false) {
                
                $dados = $this->extrairDadosEvento($schema, $xmlObj);
                $nomeArquivo = "evt-{$dados['chave']}-{$dados['tp_evento']}-{$nsu}.xml";
                
                Storage::disk('local')->put("{$pathStorage}/events/{$nomeArquivo}", $xml);

                // Primeiro, tenta achar a nota pai para vincular
                $docId = DB::table('dfe_documents')
                    ->where('company_id', $conta->company_id)
                    ->where('chave_acesso', $dados['chave'])
                    ->value('id');

                // Salva o evento
                DB::table('dfe_events')->updateOrInsert(
                    ['company_id' => $conta->company_id, 'nsu' => $nsu],
                    [
                        'dfe_document_id' => $docId, // Pode ser null se o evento chegar antes da nota
                        'chave_acesso' => $dados['chave'],
                        'tp_evento' => $dados['tp_evento'],
                        'descricao_evento' => $dados['desc'],
                        'protocolo' => $dados['proto'],
                        'data_evento' => $dados['data'],
                        'correcao_texto' => $dados['x_correcao'] ?? null,
                        'xml_path' => "{$pathStorage}/events/{$nomeArquivo}",
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]
                );
            }
        });
    }

    private function extrairDadosNFe($schema, $xml)
    {
        if (strpos($schema, 'resNFe') !== false) {
            return [
                'chave' => (string)$xml->chNFe,
                'cnpj' => (string)$xml->CNPJ,
                'nome' => (string)$xml->xNome,
                'emissao' => (string)$xml->dhEmi,
                'valor' => (float)$xml->vNF,
                'completo' => false
            ];
        } else {
            // procNFe
            $nfe = $xml->NFe->infNFe;
            return [
                'chave' => str_replace('NFe', '', (string)$nfe['Id']),
                'cnpj' => (string)$nfe->emit->CNPJ,
                'nome' => (string)$nfe->emit->xNome,
                'emissao' => (string)$nfe->ide->dhEmi,
                'valor' => (float)$nfe->total->ICMSTot->vNF,
                'completo' => true
            ];
        }
    }

    private function extrairDadosEvento($schema, $xml)
    {
        // Estrutura do evento muda pouco entre resEvento e procEvento
        // Geralmente o procEvento tem o retEvento dentro. Vamos pelo infEvento.
        $inf = isset($xml->infEvento) ? $xml->infEvento : $xml->evento->infEvento;
        
        // Se for procEventoNFe, a estrutura pode ser diferente (retEvento)
        if (isset($xml->retEvento)) {
            $inf = $xml->retEvento->infEvento;
        }

        return [
            'chave' => (string)$inf->chNFe,
            'tp_evento' => (int)$inf->tpEvento,
            'desc' => (string)$inf->xEvento,
            'proto' => (string)$inf->nProt,
            'data' => (string)$inf->dhRegEvento,
            'x_correcao' => isset($inf->detEvento->xCorrecao) ? (string)$inf->detEvento->xCorrecao : null
        ];
    }
}
