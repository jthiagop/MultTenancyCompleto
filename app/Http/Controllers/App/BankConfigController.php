<?php

namespace App\Http\Controllers\App;

use App\Http\Controllers\Controller;
use App\Models\BankConfig;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class BankConfigController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $configs = BankConfig::all();
        return response()->json($configs);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'banco_codigo' => 'required|string|max:10',
            'nome_conta' => 'nullable|string|max:255',
            'agencia' => 'nullable|string|max:10',
            'conta_corrente' => 'nullable|string|max:20',
            'client_id' => 'required|string',
            'client_secret' => 'required|string',
            'developer_app_key' => 'required|string',
            'mci_teste' => 'nullable|string|max:255',
            'convenio' => 'required|string|max:20',
            'carteira' => 'required|string|max:10',
            'variacao' => 'required|string|max:10',
            'ambiente' => 'nullable|string|in:homologacao,producao',
            'ativo' => 'nullable|boolean',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Erro de validação',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            // Processar o campo ambiente
            $ambiente = $request->has('ambiente') && $request->ambiente === 'producao' 
                ? 'producao' 
                : 'homologacao';

            $data = $validator->validated();
            $data['ambiente'] = $ambiente;
            $data['ativo'] = $request->has('ativo') ? (bool) $request->ativo : true;

            $bankConfig = BankConfig::create($data);

            return response()->json([
                'success' => true,
                'message' => 'Configuração salva com sucesso!',
                'data' => $bankConfig
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erro ao salvar configuração: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        try {
            $bankConfig = BankConfig::findOrFail($id);
            return response()->json([
                'success' => true,
                'data' => $bankConfig
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Configuração não encontrada'
            ], 404);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $validator = Validator::make($request->all(), [
            'banco_codigo' => 'sometimes|string|max:10',
            'nome_conta' => 'nullable|string|max:255',
            'agencia' => 'nullable|string|max:10',
            'conta_corrente' => 'nullable|string|max:20',
            'client_id' => 'sometimes|string',
            'client_secret' => 'sometimes|string',
            'developer_app_key' => 'sometimes|string',
            'mci_teste' => 'nullable|string|max:255',
            'convenio' => 'sometimes|string|max:20',
            'carteira' => 'sometimes|string|max:10',
            'variacao' => 'sometimes|string|max:10',
            'ambiente' => 'nullable|string|in:homologacao,producao',
            'ativo' => 'nullable|boolean',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Erro de validação',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $bankConfig = BankConfig::findOrFail($id);

            // Processar o campo ambiente
            if ($request->has('ambiente')) {
                $ambiente = $request->ambiente === 'producao' ? 'producao' : 'homologacao';
                $bankConfig->ambiente = $ambiente;
            }

            if ($request->has('ativo')) {
                $bankConfig->ativo = (bool) $request->ativo;
            }

            $bankConfig->update($validator->validated());

            return response()->json([
                'success' => true,
                'message' => 'Configuração atualizada com sucesso!',
                'data' => $bankConfig
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erro ao atualizar configuração: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        try {
            $bankConfig = BankConfig::findOrFail($id);
            $bankConfig->delete();

            return response()->json([
                'success' => true,
                'message' => 'Configuração removida com sucesso!'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erro ao remover configuração: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Test BB API connection
     */
    public function testConnection(Request $request)
    {
        // Se foi enviado um ID de configuração existente
        if ($request->has('bank_config_id')) {
            try {
                $bankConfig = BankConfig::findOrFail($request->bank_config_id);
                
                // Instanciar o serviço com a config do banco (decriptação ocorre aqui)
                $bbService = new \App\Services\Banks\BancoBrasilService($bankConfig);
                
                // Tentar autenticar
                $token = $bbService->testarAutenticacao();

                return response()->json([
                    'success' => true,
                    'message' => 'Conexão estabelecida com sucesso! Credenciais válidas.',
                    'token_preview' => substr($token, 0, 20) . '...'
                ]);

            } catch (\Illuminate\Contracts\Encryption\DecryptException $e) {
                 return response()->json([
                    'success' => false,
                    'message' => 'Erro de descriptografia: As credenciais salvas são inválidas. Por favor, edite e salve novamente.'
                ], 400);
            } catch (\Exception $e) {
                return response()->json([
                    'success' => false,
                    'message' => 'Falha na conexão: ' . $e->getMessage()
                ], 400);
            }
        }

        // Teste com dados do formulário (antes de salvar)
        $validator = Validator::make($request->all(), [
            'client_id' => 'required|string',
            'client_secret' => 'required|string',
            'developer_app_key' => 'required|string',
            'ambiente' => 'required|string|in:homologacao,producao',
            'mci_teste' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Dados incompletos para teste',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            // Criar uma instância temporária do BankConfig para teste
            $tempConfig = new BankConfig($request->all());
            
            // Instanciar o serviço
            $bbService = new \App\Services\Banks\BancoBrasilService($tempConfig);
            
            // Tentar autenticar
            $token = $bbService->testarAutenticacao();

            return response()->json([
                'success' => true,
                'message' => 'Conexão estabelecida com sucesso! Credenciais válidas.',
                'token_preview' => substr($token, 0, 20) . '...'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Falha na conexão: ' . $e->getMessage()
            ], 400);
        }
    }
    /**
     * Teste específico para API de Extratos
     * (Verifica se Agência/Conta estão corretas e se a permissão de extrato está ativa)
     */
    public function testExtrato(Request $request)
    {
        // Se foi enviado um ID de configuração existente (para usar configurações salvas)
        if ($request->has('bank_config_id')) {
             try {
                $bankConfig = BankConfig::findOrFail($request->bank_config_id);
                $service = new \App\Services\Banks\BancoBrasilService($bankConfig);
                $resultado = $service->testarApiExtrato();

                return response()->json([
                    'success' => true,
                    'message' => 'Sucesso! API de Extrato respondendo.',
                    'data' => $resultado
                ]);
             } catch (\Exception $e) {
                 return response()->json([
                    'success' => false,
                    'message' => 'Erro no teste de extrato: ' . $e->getMessage()
                ], 400);
             }
        }

        // 1. Validação dos dados necessários para extrato (Teste Manual)
        $validator = Validator::make($request->all(), [
            'client_id' => 'required',
            'client_secret' => 'required',
            'developer_app_key' => 'required',
            'ambiente' => 'required|in:homologacao,producao',
            'agencia' => 'required',
            'conta_corrente' => 'required',
            'mci_teste' => 'nullable'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Preencha Agência e Conta para testar o extrato.',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            // 2. Cria instância temporária (Memory Model)
            $tempConfig = new BankConfig($request->all());

            // 3. Chama o Service
            $service = new \App\Services\Banks\BancoBrasilService($tempConfig);
            
            // 4. Executa o teste de extrato (lógica que criamos no Service)
            $resultado = $service->testarApiExtrato();

            return response()->json([
                'success' => true,
                'message' => 'Sucesso! API de Extrato respondendo.',
                'data' => $resultado
            ]);

        } catch (\Exception $e) {
            // Tratamento amigável de erro
            $erro = $e->getMessage();
            
            // Dica visual se for erro 403/401 (Permissão)
            if (str_contains($erro, 'Forbidden') || str_contains($erro, 'Unauthorized')) {
                $erro .= " (Verifique se a App no portal developers.bb.com.br tem permissão para a API de Extratos)";
            }

            return response()->json([
                'success' => false,
                'message' => 'Erro no teste de extrato: ' . $erro
            ], 400);
        }
    }
}
