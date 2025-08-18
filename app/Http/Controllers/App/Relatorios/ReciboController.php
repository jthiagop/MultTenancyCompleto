<?php

namespace App\Http\Controllers\App\Relatorios;

use App\Http\Controllers\Controller;
use App\Models\Address;
use App\Models\Financeiro\Recibo;
use App\Models\Financeiro\TransacaoFinanceira;
use Illuminate\Http\Request;
use Spatie\Browsershot\Browsershot;
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

        $pdf = Browsershot::html($html)
            ->format('A4')                 // Define o formato como A4
            ->margins(5, 5, 5, 5)           // Margens menores para melhor aproveitamento
            ->showBackground()               // Garante que fundos CSS sejam renderizados
            ->deviceScaleFactor(2)           // Simula uma tela de alta resolução
            ->quality(100) // Garante máxima qualidade para imagens
            ->emulateMedia('screen') // Melhora a renderização do CSS no PDF
            ->pdf();

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
            return redirect()->back()
                ->withErrors($validator)
                ->withInput()
                ->with('modal_open', true); // Mantém o modal aberto
        }

        // Verifica se a transação existe
        $transacao = TransacaoFinanceira::findOrFail($transacaoId);

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
            'address_id' => $address->id, // ✅ Agora o recibo guarda o ID do endereço
            'nome' => $request->nome,
            'cpf_cnpj' => $request->cpf_cnpj,
            'valor' => $transacao->valor, // Pega o valor da transação
            'referente' => $request->referente,
        ]);

        return redirect()->back()->with('message', 'Recibo criado com sucesso!');
    }

    /**
     * Converte o caminho de uma imagem em uma string Base64.
     * (Função auxiliar que você já tinha no outro controller)
     */
    protected function logoToBase64($company): ?string
    {
        if (!$company || !$company->avatar) {
            // Caminho para uma imagem padrão caso a empresa não tenha logo
            $path = public_path('assets/media/png/perfil.svg');
        } else {
            $path = storage_path('app/public/' . $company->avatar);
        }

        if (!file_exists($path)) {
            return null; // Retorna nulo se o arquivo não for encontrado
        }

        return 'data:image/' . pathinfo($path, PATHINFO_EXTENSION) . ';base64,' . base64_encode(file_get_contents($path));
    }
}
