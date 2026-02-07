<?php

namespace App\Http\Controllers\App;

use App\Http\Controllers\Controller;
use App\Models\LancamentoPadrao;
use App\Models\Contabilide\ChartOfAccount;
use App\Exports\LancamentosPadraoTemplateExport;
use Flasher;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Yajra\DataTables\Facades\DataTables;

class LancamentoPadraoController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {

        $lps = LancamentoPadrao::all();
        $lancamentoPadrao = LancamentoPadrao::all();

        // Busca contas contábeis para os dropdowns
        $contas = ChartOfAccount::forActiveCompany()->orderBy('code')->get();

        // Mapeia categorias para classes de cor
        $categoryColors = [
            'Serviços essenciais' => 'badge-light-success',
            'Suprimentos' => 'badge-light-primary',
            'Pessoal' => 'badge-light-warning',
            'Alimentação' => 'badge-light-info',
            'Saúde' => 'badge-light-danger',
            'Manutenção' => 'badge-light-dark',
            'Liturgia' => 'badge-light-muted',
            'Equipamentos' => 'badge-light-secondary',
            'Material de escritório' => 'badge-light-light',
            'Educação' => 'badge-light-orange',
            'Transporte' => 'badge-light-teal',
            'Contribuições' => 'badge-light-purple',
            // Adicione outras categorias e cores conforme necessário
        ];

        return view('app.cadastros.lancamentoPadrao.index', compact('lps', 'categoryColors', 'lancamentoPadrao', 'contas'));
    }

    /**
     * Retorna dados para DataTable via AJAX
     */
    public function getData(Request $request)
    {
        if ($request->ajax()) {
            $companyId = session('active_company_id');
            
            $lancamentos = LancamentoPadrao::with(['contaDebito', 'contaCredito', 'user'])
                ->select('lancamento_padraos.*');

            // Filtro por tipo (tabs: entrada, saida, todos)
            $typeFilter = $request->input('type');
            if ($typeFilter && $typeFilter !== 'todos') {
                $lancamentos->where('type', $typeFilter);
            }

            $result = DataTables::of($lancamentos)
            ->addColumn('checkbox', function ($row) {
                return '<div class="form-check form-check-sm form-check-custom form-check-solid">
                            <input class="form-check-input" type="checkbox" value="' . $row->id . '" />
                        </div>';
            })
            ->addColumn('description', function ($row) {
                return '<span class="text-gray-800">' . ($row->description ?? 'N/A') . '</span>';
            })
            ->addColumn('type', function ($row) {
                $badgeClass = 'badge-light-secondary';
                $typeLabel = 'N/A';
                
                if ($row->type === 'entrada') {
                    $badgeClass = 'badge-light-success';
                    $typeLabel = 'Entrada';
                } elseif ($row->type === 'saida') {
                    $badgeClass = 'badge-light-danger';
                    $typeLabel = 'Saída';
                } elseif ($row->type === 'ambos') {
                    $badgeClass = 'badge-light-info';
                    $typeLabel = 'Ambos';
                }
                
                return '<span class="badge ' . $badgeClass . '">' . $typeLabel . '</span>';
            })
            ->addColumn('category', function ($row) {
                return '<span class="text-gray-800">' . ($row->category ?? 'N/A') . '</span>';
            })
            ->addColumn('conta_debito', function ($row) {
                if ($row->contaDebito) {
                    return '<span class="text-gray-800">' . $row->contaDebito->name . '</span><br><small class="text-muted">' . $row->contaDebito->code . '</small>';
                }
                return '<span class="text-muted">Não definida</span>';
            })
            ->addColumn('conta_credito', function ($row) {
                if ($row->conta_credito_id == 0) {
                    return '<span class="text-muted">-- Usar conta do Banco/Caixa --</span>';
                }
                if ($row->contaCredito) {
                    return '<span class="text-gray-800">' . $row->contaCredito->name . '</span><br><small class="text-muted">' . $row->contaCredito->code . '</small>';
                }
                return '<span class="text-muted">Não definida</span>';
            })
            ->addColumn('action', function ($row) {
                $deleteUrl = route('lancamentoPadrao.destroy', $row->id);
                
                return '<a href="#" class="btn btn-light btn-active-light-primary btn-sm" data-kt-menu-trigger="click" data-kt-menu-placement="bottom-end">Ações
                            <span class="svg-icon svg-icon-5 m-0">
                                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M11.4343 12.7344L7.25 8.55005C6.83579 8.13583 6.16421 8.13584 5.75 8.55005C5.33579 8.96426 5.33579 9.63583 5.75 10.05L11.2929 15.5929C11.6834 15.9835 12.3166 15.9835 12.7071 15.5929L18.25 10.05C18.6642 9.63584 18.6642 8.96426 18.25 8.55005C17.8358 8.13584 17.1642 8.13584 16.75 8.55005L12.5657 12.7344C12.2533 13.0468 11.7467 13.0468 11.4343 12.7344Z" fill="currentColor" />
                                </svg>
                            </span>
                        </a>
                        <div class="menu menu-sub menu-sub-dropdown menu-column menu-rounded menu-gray-600 menu-state-bg-light-primary fw-semibold fs-7 w-125px py-4" data-kt-menu="true">
                            <div class="menu-item px-3">
                                <a href="#" class="menu-link px-3" onclick="window.openLancamentoPadraoForEdit(' . $row->id . '); return false;">Editar</a>
                            </div>
                            <div class="menu-item px-3">
                                <a href="#" class="menu-link px-3" data-kt-lancamento-padrao-table-filter="delete_row" data-id="' . $row->id . '">Excluir</a>
                            </div>
                        </div>';
            })
            ->rawColumns(['checkbox', 'description', 'type', 'category', 'conta_debito', 'conta_credito', 'action'])
            ->make(true);
            
            \Log::info('LancamentoPadraoController::getData - Resposta gerada');
            return $result;
        }
        
        \Log::warning('LancamentoPadraoController::getData - Requisição não é AJAX');
        return response()->json(['error' => 'Requisição inválida.'], 400);
    }

    /**
     * Retorna contagem por tipo para as tabs segmentadas
     */
    public function getStats(Request $request)
    {
        $todos   = LancamentoPadrao::count();
        $entrada = LancamentoPadrao::where('type', 'entrada')->count();
        $saida   = LancamentoPadrao::where('type', 'saida')->count();

        return response()->json([
            'todos'   => $todos,
            'entrada' => $entrada,
            'saida'   => $saida,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request)
    {
        // Obtém o tipo selecionado do request
        $tipo = $request->input('tipo');
        $lps = LancamentoPadrao::all();

        // Busca contas contábeis para os dropdowns
        $contas = ChartOfAccount::forActiveCompany()->orderBy('code')->get();

        // Se um tipo foi selecionado, busque os lançamentos correspondentes
        $lancamentos = $tipo ? LancamentoPadrao::where('tipo', $tipo)->get() : collect();

        return view('app.cadastros.lancamentoPadrao.create', compact('lancamentos', 'tipo', 'lps', 'contas'));
    }

    /**
     * Valida um campo específico via AJAX
     */
    public function validateField(Request $request)
    {
        $field = $request->input('field');
        $value = $request->input('value');

        // Se o campo estiver vazio e for opcional, retorna válido
        if (empty($value) && in_array($field, ['conta_debito_id', 'conta_credito_id'])) {
            return response()->json([
                'valid' => true,
                'message' => ''
            ]);
        }

        // Regras de validação baseadas no campo
        $rules = [
            'description' => 'required|string|max:255',
            'type' => 'required|in:entrada,saida,ambos',
            'category' => 'required|string|max:255',
            'conta_debito_id' => 'nullable|exists:chart_of_accounts,id',
            'conta_credito_id' => [
                'nullable',
                function ($attribute, $value, $fail) {
                    // Permite valor "0" que significa usar conta do banco/caixa
                    if ($value == '0') {
                        return;
                    }
                    // Valida se existe na tabela chart_of_accounts
                    if ($value && !\App\Models\Contabilide\ChartOfAccount::where('id', $value)->exists()) {
                        $fail('A conta de crédito selecionada não existe.');
                    }
                },
            ],
        ];

        // Mensagens de erro personalizadas
        $messages = [
            'description.required' => 'O nome do lançamento é obrigatório.',
            'description.max' => 'O nome do lançamento não pode ter mais de 255 caracteres.',
            'type.required' => 'O tipo do lançamento é obrigatório.',
            'type.in' => 'O tipo deve ser "entrada", "saída" ou "ambos".',
            'category.required' => 'A categoria é obrigatória.',
            'conta_debito_id.exists' => 'A conta de débito selecionada não existe.',
            'conta_credito_id.exists' => 'A conta de crédito selecionada não existe.'
        ];

        // Valida apenas o campo específico
        $validator = \Validator::make(
            [$field => $value],
            [$field => $rules[$field] ?? 'nullable'],
            $messages
        );

        if ($validator->fails()) {
            return response()->json([
                'valid' => false,
                'message' => $validator->errors()->first($field)
            ]);
        }

        return response()->json([
            'valid' => true,
            'message' => ''
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Validação dos dados
        $rules = [
            'description' => 'required|string|max:255',
            'type' => 'required|in:entrada,saida,ambos',
            'category' => 'required|string|max:255',
            'conta_debito_id' => 'required|exists:chart_of_accounts,id',
            'conta_credito_id' => [
                'required',
                function ($attribute, $value, $fail) {
                    // Permite valor "0" que significa usar conta do banco/caixa
                    if ($value == '0' || $value === 0) {
                        return;
                    }
                    // Valida se existe na tabela chart_of_accounts
                    if ($value && !\App\Models\Contabilide\ChartOfAccount::where('id', $value)->exists()) {
                        $fail('A conta de crédito selecionada não existe.');
                    }
                },
            ],
        ];

        $messages = [
            'description.required' => 'O nome do lançamento é obrigatório.',
            'type.required' => 'O tipo do lançamento é obrigatório.',
            'type.in' => 'O tipo deve ser "entrada", "saída" ou "ambos".',
            'category.required' => 'A categoria é obrigatória.',
            'conta_debito_id.required' => 'A conta de débito é obrigatória.',
            'conta_debito_id.exists' => 'A conta de débito selecionada não existe.',
            'conta_credito_id.required' => 'A conta de crédito é obrigatória.',
        ];

        $request->validate($rules, $messages);

        $user = Auth::user(); // Usuário autenticado
        $companyId = session('active_company_id');

        // Criação do lançamento
        LancamentoPadrao::create([
            'type' => $request->input('type'),
            'description' => $request->input('description'),
            'category' => $request->input('category'),
            'user_id' => $user->id, // Pegando o ID do usuário autenticado
            'company_id' => $companyId, // ID da empresa ativa
            'conta_debito_id' => $request->input('conta_debito_id'),
            'conta_credito_id' => $request->input('conta_credito_id') == '0' ? null : $request->input('conta_credito_id'),
        ]);

        // Se for requisição AJAX, retorna JSON
        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Lançamento cadastrado com sucesso!'
            ]);
        }

            // Adiciona mensagem de sucesso
        Flasher::addSuccess('Lançamento cadastrado com sucesso!');
        return redirect()->back()->with('message', 'Lançamento Padrão criado com sucesso!');
    }



    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Request $request, string $id)
    {
        $lp = LancamentoPadrao::findOrFail($id);

        // Se for requisição AJAX, retornar JSON
        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'data' => [
                    'id' => $lp->id,
                    'description' => $lp->description,
                    'type' => $lp->type,
                    'category' => $lp->category,
                    'conta_debito_id' => $lp->conta_debito_id ? (string)$lp->conta_debito_id : null,
                    'conta_credito_id' => $lp->conta_credito_id === null || $lp->conta_credito_id === 0 ? '0' : (string)$lp->conta_credito_id,
                ]
            ]);
        }

        $lps = LancamentoPadrao::all();

        // Busca contas contábeis para os dropdowns
        $contas = ChartOfAccount::forActiveCompany()->orderBy('code')->get();

        return view('app.cadastros.lancamentoPadrao.edit', ['lps' => $lps, 'lp' => $lp, 'contas' => $contas ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        // Validação dos dados
        $validator = \Validator::make($request->all(), [
            'description' => 'required|string|max:255',
            'type' => 'required|in:entrada,saida,ambos',
            'category' => 'required|string|max:255',
            'conta_debito_id' => 'required|exists:chart_of_accounts,id',
            'conta_credito_id' => [
                'required',
                function ($attribute, $value, $fail) {
                    if ($value == '0') {
                        return; // Permite valor "0" que significa usar conta do banco/caixa
                    }
                    if ($value && !\App\Models\Contabilide\ChartOfAccount::where('id', $value)->exists()) {
                        $fail('A conta de crédito selecionada não existe.');
                    }
                },
            ],
        ], [
            'description.required' => 'O nome do lançamento é obrigatório.',
            'type.required' => 'O tipo do lançamento é obrigatório.',
            'type.in' => 'O tipo deve ser "entrada", "saída" ou "ambos".',
            'category.required' => 'A categoria é obrigatória.',
            'conta_debito_id.required' => 'A conta de débito é obrigatória.',
            'conta_debito_id.exists' => 'A conta de débito selecionada não existe.',
            'conta_credito_id.required' => 'A conta de crédito é obrigatória.',
        ]);

        // Se houver erros de validação, retornar JSON para AJAX
        if ($validator->fails()) {
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'errors' => $validator->errors()
                ], 422);
            }
            return redirect()->back()->withErrors($validator)->withInput();
        }

        // Encontra o lançamento padrão pelo ID
        $lancamento = LancamentoPadrao::findOrFail($id);

        // Atualiza os dados do lançamento
        $lancamento->update([
            'description' => $request->description,
            'type' => $request->type,
            'category' => $request->category,
            'conta_debito_id' => $request->input('conta_debito_id'),
            'conta_credito_id' => $request->input('conta_credito_id') == '0' ? null : $request->input('conta_credito_id'),
        ]);

        // Se for requisição AJAX, retornar JSON
        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Lançamento Padrão atualizado com sucesso!'
            ]);
        }

        // Redireciona com uma mensagem de sucesso
        return redirect()->route('lancamentoPadrao.create')->with('success', 'Lançamento Padrão atualizado com sucesso!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request, string $id)
    {
        try {
            // Localiza o registro pelo ID
            $lancamento = LancamentoPadrao::findOrFail($id);

            // Exclui o registro
            $lancamento->delete();

            // Se for requisição AJAX, retorna JSON
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Lançamento Padrão excluído com sucesso!'
                ]);
            }

            // Redireciona com uma mensagem de sucesso
            return redirect()->route('lancamentoPadrao.index')->with('success', 'Lançamento Padrão excluído com sucesso!');
        } catch (\Exception $e) {
            // Log do erro (opcional)
            \Log::error('Erro ao excluir Lançamento Padrão: ' . $e->getMessage());

            // Se for requisição AJAX, retorna JSON
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Erro ao excluir Lançamento Padrão.'
                ], 500);
            }

            // Redireciona com uma mensagem de erro
            return redirect()->route('lancamentoPadrao.index')->with('error', 'Erro ao excluir Lançamento Padrão.');
        }
    }

    /**
     * Download do template Excel para edição em massa
     */
    public function downloadTemplate()
    {
        $companyId = session('active_company_id');
        
        if (!$companyId) {
            if (request()->ajax() || request()->wantsJson()) {
                return response()->json(['error' => 'Nenhuma empresa selecionada.'], 403);
            }
            return redirect()->back()->with('error', 'Nenhuma empresa selecionada.');
        }
        
        try {
            $export = new LancamentosPadraoTemplateExport($companyId);
            $spreadsheet = $export->generate();
            
            $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
            $filename = 'lancamentos_padrao_template.xlsx';
            $tempFile = tempnam(sys_get_temp_dir(), $filename);
            $writer->save($tempFile);
            
            return response()->download($tempFile, $filename)->deleteFileAfterSend(true);
        } catch (\Exception $e) {
            \Log::error('Erro ao gerar template Excel: ' . $e->getMessage());
            
            if (request()->ajax() || request()->wantsJson()) {
                return response()->json(['error' => 'Erro ao gerar template: ' . $e->getMessage()], 500);
            }
            return redirect()->back()->with('error', 'Erro ao gerar template Excel.');
        }
    }

    /**
     * Upload e processamento do template Excel para atualização em massa
     */
    public function uploadTemplate(Request $request)
    {
        $companyId = session('active_company_id');
        
        if (!$companyId) {
            return response()->json([
                'success' => false,
                'message' => 'Nenhuma empresa selecionada.'
            ], 403);
        }

        // Validação do arquivo
        $request->validate([
            'file' => 'required|file|mimes:xlsx|max:10240', // 10MB
        ], [
            'file.required' => 'Por favor, selecione um arquivo.',
            'file.mimes' => 'O arquivo deve ser do tipo .xlsx',
            'file.max' => 'O arquivo não pode ser maior que 10MB.',
        ]);

        try {
            $file = $request->file('file');
            $reader = new \PhpOffice\PhpSpreadsheet\Reader\Xlsx();
            $spreadsheet = $reader->load($file->getRealPath());
            
            // Pega a aba de Lançamentos
            $lancamentosSheet = $spreadsheet->getSheetByName('Lançamentos');
            if (!$lancamentosSheet) {
                return response()->json([
                    'success' => false,
                    'message' => 'A aba "Lançamentos" não foi encontrada no arquivo.'
                ], 400);
            }

            $rows = $lancamentosSheet->getHighestRow();
            $updated = 0;
            $errors = [];

            // Processa cada linha (começando da linha 2, pois linha 1 é cabeçalho)
            for ($row = 2; $row <= $rows; $row++) {
                $id = $lancamentosSheet->getCell('A' . $row)->getValue();
                $descricao = $lancamentosSheet->getCell('B' . $row)->getValue();
                $tipo = $lancamentosSheet->getCell('C' . $row)->getValue();
                $contaDebitoStr = $lancamentosSheet->getCell('D' . $row)->getValue();
                $contaCreditoStr = $lancamentosSheet->getCell('E' . $row)->getValue();

                // Pula linhas vazias
                if (empty($id) && empty($descricao)) {
                    continue;
                }

                // Busca o lançamento pelo ID
                $lancamento = LancamentoPadrao::where('id', $id)
                    ->where('company_id', $companyId)
                    ->first();

                if (!$lancamento) {
                    $errors[] = "Linha {$row}: Lançamento com ID {$id} não encontrado.";
                    continue;
                }

                // Processa conta de débito
                $contaDebitoId = null;
                if (!empty($contaDebitoStr)) {
                    $contaDebitoId = $this->parseContaFromString($contaDebitoStr, $companyId);
                    if (!$contaDebitoId) {
                        $errors[] = "Linha {$row}: Conta de débito '{$contaDebitoStr}' não encontrada.";
                        continue;
                    }
                }

                // Processa conta de crédito
                $contaCreditoId = null;
                if (!empty($contaCreditoStr)) {
                    $contaCreditoId = $this->parseContaFromString($contaCreditoStr, $companyId);
                    if (!$contaCreditoId) {
                        $errors[] = "Linha {$row}: Conta de crédito '{$contaCreditoStr}' não encontrada.";
                        continue;
                    }
                }

                // Atualiza o lançamento
                $lancamento->update([
                    'description' => $descricao ?: $lancamento->description,
                    'type' => $tipo ?: $lancamento->type,
                    'conta_debito_id' => $contaDebitoId ?: $lancamento->conta_debito_id,
                    'conta_credito_id' => $contaCreditoId ?: $lancamento->conta_credito_id,
                ]);

                $updated++;
            }

            // Prepara resposta
            $message = "Arquivo processado com sucesso! {$updated} lançamento(s) atualizado(s).";
            if (count($errors) > 0) {
                $message .= " " . count($errors) . " erro(s) encontrado(s).";
            }

            return response()->json([
                'success' => true,
                'message' => $message,
                'updated' => $updated,
                'errors' => $errors,
                'registro' => [
                    'data' => now()->format('d/m/Y H:i'),
                    'nome_arquivo' => $file->getClientOriginalName(),
                    'produtos' => $updated,
                    'status' => count($errors) > 0 ? 'parcial' : 'processado'
                ]
            ]);

        } catch (\Exception $e) {
            \Log::error('Erro ao processar upload de template: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Erro ao processar arquivo: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Extrai o código da conta a partir da string "Código - Nome"
     */
    private function parseContaFromString($string, $companyId)
    {
        if (empty($string)) {
            return null;
        }

        // Formato esperado: "1.01.001 - CAIXA GERAL"
        $parts = explode(' - ', $string);
        $code = trim($parts[0]);

        if (empty($code)) {
            return null;
        }

        // Busca a conta pelo código
        $conta = ChartOfAccount::where('company_id', $companyId)
            ->where('code', $code)
            ->first();

        return $conta ? $conta->id : null;
    }

}
