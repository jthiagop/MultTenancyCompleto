<?php

namespace App\Http\Controllers\App\Relatorios;

use App\Http\Controllers\Controller;
use App\Models\Address;
use App\Models\Financeiro\Recibo;
use App\Models\Financeiro\TransacaoFinanceira;
use App\Models\Parceiro;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Spatie\Browsershot\Browsershot;
use App\Helpers\BrowsershotHelper;
use Validator;

class ReciboController extends Controller
{
    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        // Buscar o recibo junto com o relacionamento de endereço
        $recibo = Recibo::with('address')->findOrFail($id);

        // Se houver um endereço vinculado
        if ($recibo->address) {
            // Verifica se o endereço está associado apenas a este recibo
            if ($recibo->address->recibos()->count() === 1) {
                // Excluir o endereço, pois não está sendo usado por outros registros
                $recibo->address->delete();
            }
        }

        // Excluir o recibo
        $recibo->delete();

        return redirect()->back()->with('message', 'Recibo excluídos com sucesso!');
    }

    // *** Gerar Recibo ***

    public function imprimirRecibo($reciboId)
    {
        // 1) Buscar o recibo com seus relacionamentos
        $recibo = Recibo::with(['address', 'transacao'])->findOrFail($reciboId);
        $activeCompanyId = session('active_company_id');
        $company = \App\Models\Company::with('addresses')->find($activeCompanyId);

        if (!$company) {
            abort(404, 'Empresa não encontrada ou não selecionada.');
        }
        // Chama a nossa nova função para obter o logo em Base64
        $companyLogo = $this->logoToBase64($company);

        // 2) Preparar a view para o PDF (Ex: 'app.financeiro.recibo.pdf')
        //    Você pode criar um arquivo Blade que contenha o layout do recibo
        //    e enviar o objeto $recibo para ele.
        $html = view('app.relatorios.financeiro.recibo', [
            'recibo' => $recibo,
            'company' => $company,
            'companyLogo' => $companyLogo, // A variável agora existe e está sendo passada
        ])->render();

        $pdf = BrowsershotHelper::configureChromePath(
            Browsershot::html($html)
                ->format('A4')                 // Define o formato como A4
                ->margins(5, 5, 5, 5)           // Margens menores para melhor aproveitamento
                ->showBackground()               // Garante que fundos CSS sejam renderizados
                ->deviceScaleFactor(2)           // Simula uma tela de alta resolução
                ->quality(100) // Garante máxima qualidade para imagens
                ->emulateMedia('screen') // Melhora a renderização do CSS no PDF
        )->pdf();

        // 4) Retornar o PDF diretamente como resposta ao navegador
        return response($pdf)
            ->withHeaders([
                'Content-Type'        => 'application/pdf',
                'Content-Disposition' => 'inline; filename="recibo.pdf"',
            ]);
    }

    public function gerarRecibo(Request $request, $transacaoId)
    {
        $validator = Validator::make($request->all(), [
            'nome' => 'required|string|max:255',
            'cpf_cnpj' => 'required',
            'valor' => 'required',
            'data_emissao' => 'required|date_format:d/m/Y',
            'referente' => 'required',
        ]);


        if ($validator->fails()) {
            // Se for requisição AJAX, retornar JSON
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'errors' => $validator->errors()
                ], 422);
            }
            
            return redirect()->back()
                ->withErrors($validator)
                ->withInput()
                ->with('modal_open', true); // Mantém o modal aberto
        }

        // Verifica se a transação existe
        $transacao = TransacaoFinanceira::findOrFail($transacaoId);

        // Bloquear recibo duplicado para a mesma transação
        $reciboExistente = Recibo::where('transacao_id', $transacao->id)->first();
        if ($reciboExistente) {
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Já existe um recibo para esta transação.',
                ], 409);
            }
            return redirect()->back()->with('error', 'Já existe um recibo para esta transação.');
        }

        // Criar ou atualizar o endereço (sem transacao_id)
        $address = Address::updateOrCreate(
            [
                'cep' => $request->cep,
                'rua' => $request->logradouro,
                'numero' => $request->numero,
                'bairro' => $request->bairro,
                'complemento' => $request->complemento,
                'cidade' => $request->localidade,
                'uf' => $request->uf,
            ]
        );

        // Criar o recibo vinculado à transação e ao endereço
        $recibo = Recibo::create([
            'transacao_id' => $transacao->id,
            'address_id' => $address->id,
            'nome' => $request->nome,
            'cpf_cnpj' => $request->cpf_cnpj,
            'valor' => $transacao->valor,
            'referente' => $request->referente,
        ]);

        // =============================================
        // Criar ou vincular Parceiro automaticamente
        // Entrada = Cliente | Saída = Fornecedor
        // =============================================
        $this->vincularOuCriarParceiro($transacao, $request, $address);

        // Se for requisição AJAX, retornar JSON de sucesso com URL do PDF
        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Recibo criado com sucesso!',
                'recibo_id' => $recibo->id,
                'pdf_url' => route('recibo.imprimir', $recibo->id)
            ]);
        }

        // Se solicitado, redirecionar para a impressão do recibo
        if ($request->has('redirect_to_print') && $request->redirect_to_print == 'true') {
            return redirect()->route('recibo.imprimir', $recibo->id);
        }

        return redirect()->back()->with('message', 'Recibo criado com sucesso!');
    }

    /**
     * Vincula um parceiro existente ou cria um novo a partir dos dados do recibo.
     *
     * Regra de negócio:
     * - Entrada (receita) → parceiro é Cliente
     * - Saída (despesa)   → parceiro é Fornecedor
     *
     * Se a transação já tem parceiro vinculado, apenas atualiza o endereço (se vazio).
     * Se não tem parceiro, busca por CPF/CNPJ. Se encontrar, vincula. Se não, cria novo.
     */
    protected function vincularOuCriarParceiro(TransacaoFinanceira $transacao, Request $request, Address $address): void
    {
        $activeCompanyId = session('active_company_id');
        $cpfCnpjLimpo = preg_replace('/\D/', '', $request->cpf_cnpj);

        if (empty($cpfCnpjLimpo)) {
            return; // Sem documento, não há como buscar/criar parceiro
        }

        // Determinar natureza: entrada = cliente, saída = fornecedor
        $natureza = $transacao->tipo === 'entrada' ? 'cliente' : 'fornecedor';

        // Determinar tipo de documento e pessoa
        $isCpf = strlen($cpfCnpjLimpo) <= 11;
        $tipoPessoa = $isCpf ? 'pf' : 'pj';
        $campoDoc = $isCpf ? 'cpf' : 'cnpj';

        // Se a transação já tem parceiro, apenas atualizar endereço se estiver vazio
        if ($transacao->parceiro_id) {
            $parceiroExistente = Parceiro::find($transacao->parceiro_id);
            if ($parceiroExistente && !$parceiroExistente->address_id && $address->id) {
                $parceiroExistente->update(['address_id' => $address->id]);
            }
            // Atualizar natureza para 'ambos' se necessário
            if ($parceiroExistente && $parceiroExistente->natureza !== $natureza && $parceiroExistente->natureza !== 'ambos') {
                $parceiroExistente->update(['natureza' => 'ambos']);
            }
            return;
        }

        // Buscar parceiro existente pelo documento na mesma empresa
        $parceiro = Parceiro::where('company_id', $activeCompanyId)
            ->where($campoDoc, $cpfCnpjLimpo)
            ->first();

        if ($parceiro) {
            // Parceiro encontrado — vincular à transação
            $transacao->update(['parceiro_id' => $parceiro->id]);

            // Atualizar endereço do parceiro se não tiver
            if (!$parceiro->address_id && $address->id) {
                $parceiro->update(['address_id' => $address->id]);
            }

            // Atualizar natureza para 'ambos' se necessário
            if ($parceiro->natureza !== $natureza && $parceiro->natureza !== 'ambos') {
                $parceiro->update(['natureza' => 'ambos']);
            }
        } else {
            // Parceiro não existe — criar novo
            try {
                DB::beginTransaction();

                $parceiro = Parceiro::create([
                    'nome' => $request->nome,
                    'tipo' => $tipoPessoa,
                    'natureza' => $natureza,
                    $campoDoc => $cpfCnpjLimpo,
                    'company_id' => $activeCompanyId,
                    'address_id' => $address->id,
                    'active' => true,
                    'created_by' => Auth::id(),
                    'created_by_name' => Auth::user()->name ?? null,
                ]);

                // Vincular o novo parceiro à transação
                $transacao->update(['parceiro_id' => $parceiro->id]);

                DB::commit();
            } catch (\Exception $e) {
                DB::rollBack();
                // Log do erro mas não impede a geração do recibo
                \Log::warning('Falha ao criar parceiro automaticamente via recibo: ' . $e->getMessage());
            }
        }
    }

    /**
     * Converte o caminho de uma imagem em uma string Base64.
     * (Função auxiliar que você já tinha no outro controller)
     */
    protected function logoToBase64($company): ?string
    {
        if (!$company || !$company->avatar) {
            // Caminho para uma imagem padrão caso a empresa não tenha logo
            $path = public_path('tenancy/assets/media/png/perfil.svg');
        } else {
            $path = storage_path('app/public/' . $company->avatar);
        }

        if (!file_exists($path)) {
            return null; // Retorna nulo se o arquivo não for encontrado
        }

        return 'data:image/' . pathinfo($path, PATHINFO_EXTENSION) . ';base64,' . base64_encode(file_get_contents($path));
    }
}
