<?php

namespace App\Http\Controllers\App;

use App\Http\Controllers\Controller;
use App\Models\Fiel;
use App\Models\Address;
use App\Models\FielContact;
use App\Models\FielComplementaryData;
use App\Models\FielTithe;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;


class FielController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        // Obtém o ID da empresa do usuário autenticado
        $companyId = session('active_company_id');

        if ($request->ajax()) {
            $fieis = Fiel::with(['company', 'contacts', 'addresses', 'tithe'])
                ->where('company_id', $companyId)
                ->select('fieis.*');

            return \Yajra\DataTables\Facades\DataTables::of($fieis)
                ->addColumn('checkbox', function ($row) {
                    return '<div class="form-check form-check-sm form-check-custom form-check-solid">
                                <input class="form-check-input" type="checkbox" value="' . $row->id . '" />
                            </div>';
                })
                ->addColumn('nome_completo', function ($row) {
                    $avatar = $row->avatar ? route('file', ['path' => $row->avatar]) : '/assets/media/png/perfil.svg';

                    // Buscar endereço principal
                    $enderecoPrincipal = $row->addresses->where('pivot.tipo', 'principal')->first();
                    $enderecoTexto = '';
                    if ($enderecoPrincipal) {
                        $enderecoPartes = array_filter([
                            $enderecoPrincipal->rua,
                            $enderecoPrincipal->bairro,
                            $enderecoPrincipal->cidade,
                            $enderecoPrincipal->uf
                        ]);
                        $enderecoTexto = !empty($enderecoPartes) ? implode(', ', $enderecoPartes) : '';
                    }

                    return '<div class="d-flex align-items-center">
                                <div class="symbol symbol-circle symbol-35px overflow-hidden me-3">
                                    <div class="symbol-label">
                                        <img src="' . $avatar . '" alt="' . $row->nome_completo . '" class="w-100" />
                                    </div>
                                </div>
                                <div class="d-flex flex-column">
                                    <a href="#" class="text-gray-800 text-hover-primary fw-bold mb-1">' . $row->nome_completo . '</a>
                                    ' . ($enderecoTexto ? '<span class="text-gray-600 fs-7">' . $enderecoTexto . '</span>' : '<span class="text-gray-400 fs-7">Endereço não cadastrado</span>') . '
                                </div>
                            </div>';
                })
                ->addColumn('sexo', function ($row) {
                    $sexoTexto = $row->sexo === 'M' ? 'Masculino' : ($row->sexo === 'F' ? 'Feminino' : '');
                    return '<span class="text-gray-800">' . $sexoTexto . '</span>';
                })
                ->addColumn('cpf', function ($row) {
                    return '<span class="text-gray-800">' . ($row->cpf ?? '-') . '</span>';
                })
                ->addColumn('rg', function ($row) {
                    return '<span class="text-gray-800">' . ($row->rg ?? '-') . '</span>';
                })
                ->addColumn('telefone', function ($row) {
                    $telefone = $row->contacts->where('tipo', 'telefone')->first()->valor ?? '-';
                    return '<span class="text-gray-800">' . $telefone . '</span>';
                })
                ->addColumn('dizimista', function ($row) {
                    $isDizimista = $row->tithe && $row->tithe->dizimista == 1;
                    $badgeClass = $isDizimista ? 'badge-success' : 'badge-light';
                    $texto = $isDizimista ? 'Sim' : 'Não';
                    return '<div class="badge fw-bold ' . $badgeClass . '">' . $texto . '</div>';
                })
                ->addColumn('email', function ($row) {
                    $email = $row->contacts->where('tipo', 'email')->first()->valor ?? 'E-mail não cadastrado';
                    return '<a href="#" class="text-gray-600 text-hover-primary mb-1">' . $email . '</a>';
                })
                ->addColumn('company', function ($row) {
                    return $row->company->name ?? '';
                })
                ->addColumn('status', function ($row) {
                    $badgeClass = $row->status === 'Ativo' ? 'badge-success' : 'badge-danger';
                    return '<div class="badge fw-bold ' . $badgeClass . '">' . $row->status . '</div>';
                })
                ->editColumn('data_nascimento', function ($row) {
                    return $row->data_nascimento ? \Carbon\Carbon::parse($row->data_nascimento)->format('d/m/Y') : '';
                })
                ->addColumn('action', function ($row) {
                    return '<a href="#" class="btn btn-sm btn-light btn-active-light-primary" data-kt-menu-trigger="click" data-kt-menu-placement="bottom-end">Actions
                                <span class="svg-icon svg-icon-5 m-0">
                                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                        <path d="M11.4343 12.7344L7.25 8.55005C6.83579 8.13583 6.16421 8.13584 5.75 8.55005C5.33579 8.96426 5.33579 9.63583 5.75 10.05L11.2929 15.5929C11.6834 15.9835 12.3166 15.9835 12.7071 15.5929L18.25 10.05C18.6642 9.63584 18.6642 8.96426 18.25 8.55005C17.8358 8.13584 17.1642 8.13584 16.75 8.55005L12.5657 12.7344C12.2533 13.0468 11.7467 13.0468 11.4343 12.7344Z" fill="currentColor" />
                                    </svg>
                                </span>
                            </a>
                            <div class="menu menu-sub menu-sub-dropdown menu-column menu-rounded menu-gray-600 menu-state-bg-light-primary fw-semibold fs-7 w-125px py-4" data-kt-menu="true">
                                <div class="menu-item px-3">
                                    <a href="#" class="menu-link px-3" data-kt-fiel-table-filter="edit_row" data-id="' . $row->id . '">Editar</a>
                                </div>
                                <div class="menu-item px-3">
                                    <a href="#" class="menu-link px-3">Visualizar</a>
                                </div>
                                <div class="menu-item px-3">
                                    <a href="#" class="menu-link px-3" data-kt-customer-table-filter="delete_row" data-id="' . $row->id . '">Excluir</a>
                                </div>
                            </div>';
                })
                ->rawColumns(['checkbox', 'nome_completo', 'email', 'status', 'sexo', 'cpf', 'rg', 'telefone', 'dizimista', 'action'])
                ->make(true);
        }

        // Estatísticas para o dashboard
        $totalFieis = Fiel::where('company_id', $companyId)->count();
        $totalHomens = Fiel::where('company_id', $companyId)->where('sexo', 'M')->count();
        $totalMulheres = Fiel::where('company_id', $companyId)->where('sexo', 'F')->count();
        $totalDizimistas = Fiel::where('company_id', $companyId)
            ->whereHas('tithe', function ($query) {
                $query->where('dizimista', true);
            })->count();

        // Cálculo de porcentagens (evitando divisão por zero)
        $porcentagemHomens = $totalFieis > 0 ? number_format(($totalHomens / $totalFieis) * 100, 2) : 0;
        $porcentagemMulheres = $totalFieis > 0 ? number_format(($totalMulheres / $totalFieis) * 100, 2) : 0;
        $porcentagemDizimistas = $totalFieis > 0 ? number_format(($totalDizimistas / $totalFieis) * 100, 2) : 0;

        // Buscar profissões ativas ordenadas por popularidade
        $profissoes = \App\Models\Profissao::where('ativo', true)
            ->orderBy('popularidade')
            ->get();

        return view('app.cadastros.fieis.index', compact(
            'totalFieis',
            'totalHomens',
            'totalMulheres',
            'totalDizimistas',
            'porcentagemHomens',
            'porcentagemMulheres',
            'porcentagemDizimistas',
            'profissoes'
        ));
    }

    /**
     * API specific index for Mobile App
     */
    public function apiIndex(Request $request)
    {
        $user = auth()->user();
        $companyId = $user->company_id;

        if (!$companyId && method_exists($user, 'companies')) {
             $firstCompany = $user->companies()->first();
             if ($firstCompany) {
                 $companyId = $firstCompany->id;
             }
        }

        if (!$companyId) {
             return response()->json(['data' => []]);
        }

        $fieis = Fiel::with(['contacts', 'addresses', 'tithe'])
            ->where('company_id', $companyId)
            ->select('fieis.*')
            ->orderBy('nome_completo')
            ->paginate(20);

        return response()->json($fieis);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            // Validação dos dados
            $validator = Validator::make($request->all(), [
                'nome_completo' => 'required|string|max:255',
                'cpf' => 'nullable|string|max:14|unique:fieis,cpf',
                'rg' => 'nullable|string|max:20',
                'data_nascimento' => ['nullable', function ($attribute, $value, $fail) {
                    if ($value) {
                        // Tentar parsear no formato brasileiro d/m/Y
                        $date = \Carbon\Carbon::createFromFormat('d/m/Y', $value);
                        if (!$date || $date->format('d/m/Y') !== $value) {
                            // Tentar formato padrão Y-m-d
                            $date = \Carbon\Carbon::parse($value);
                            if (!$date) {
                                $fail('A data de nascimento deve estar no formato dd/mm/aaaa.');
                            }
                        }
                    }
                }],
                'sexo' => 'required|in:M,F,Outro',
                'avatar' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Erro de validação',
                    'errors' => $validator->errors()
                ], 422);
            }

            DB::beginTransaction();

            // Obter o ID da empresa do usuário autenticado
            $companyId = session('active_company_id');

            // Processar data de nascimento (aceita formato brasileiro d/m/Y)
            $dataNascimento = null;
            if ($request->data_nascimento) {
                try {
                    // Tentar formato brasileiro primeiro (d/m/Y)
                    if (preg_match('/^\d{2}\/\d{2}\/\d{4}$/', $request->data_nascimento)) {
                        $dataNascimento = \Carbon\Carbon::createFromFormat('d/m/Y', $request->data_nascimento)->format('Y-m-d');
                    } else {
                        // Tentar formato padrão (Y-m-d)
                        $dataNascimento = \Carbon\Carbon::parse($request->data_nascimento)->format('Y-m-d');
                    }
                } catch (\Exception $e) {
                    // Se falhar, tentar parse genérico
                    $dataNascimento = \Carbon\Carbon::parse($request->data_nascimento)->format('Y-m-d');
            }
            }

            // Dados básicos do fiel
            $fielData = [
                'company_id' => $companyId,
                'nome_completo' => $request->nome_completo,
                'cpf' => $request->cpf,
                'rg' => $request->rg,
                'data_nascimento' => $dataNascimento,
                'sexo' => $request->sexo,
                'notifications' => json_encode($request->input('notifications', [])),
                'status' => $request->status ?? 'Ativo',
                'created_by' => auth()->user()->id,
                'updated_by' => auth()->user()->id,
                'created_by_name' => auth()->user()->name,
            ];

            // Upload do avatar
            if ($request->hasFile('avatar')) {
                $avatar = $request->file('avatar');
                $avatarPath = $avatar->store('avatars', 'public');
                $fielData['avatar'] = $avatarPath;
            }

            // Criar o fiel
            $fiel = Fiel::create($fielData);

            // Salvar contatos (telefones e email)
            if ($request->telefone) {
                FielContact::create([
                    'fiel_id' => $fiel->id,
                    'tipo' => 'telefone',
                    'valor' => $request->telefone,
                    'principal' => true,
                ]);
            }

            if ($request->telefone_secundario) {
                FielContact::create([
                    'fiel_id' => $fiel->id,
                    'tipo' => 'telefone_secundario',
                    'valor' => $request->telefone_secundario,
                    'principal' => false,
                ]);
            }

            if ($request->email) {
                FielContact::create([
                    'fiel_id' => $fiel->id,
                    'tipo' => 'email',
                    'valor' => $request->email,
                    'principal' => true,
                ]);
            }

            // Salvar dados complementares
            if ($request->profissao || $request->estado_civil) {
                FielComplementaryData::create([
                    'fiel_id' => $fiel->id,
                    'profissao' => $request->profissao,
                    'estado_civil' => $request->estado_civil,
                ]);
            }

            // Salvar endereço
            if ($request->cep || $request->endereco || $request->bairro || $request->cidade || $request->estado) {
                $address = Address::create([
                    'company_id' => $companyId,
                    'cep' => $request->cep,
                    'rua' => $request->endereco,
                    'bairro' => $request->bairro,
                    'cidade' => $request->cidade,
                    'uf' => $request->estado,
                ]);

                // Relacionar endereço com o fiel
                $fiel->addresses()->attach($address->id, ['tipo' => 'principal']);
            }

            // Salvar dados de dízimo
            if ($request->has('dizimista') && $request->boolean('dizimista')) {
                FielTithe::create([
                    'fiel_id' => $fiel->id,
                    'dizimista' => true,
                ]);
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Fiel cadastrado com sucesso!',
                'fiel' => $fiel
            ], 200);

        } catch (\Illuminate\Validation\ValidationException $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Erro de validação',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Ocorreu um erro ao cadastrar o fiel: ' . $e->getMessage()
            ], 500);
        }
    }



    /**
     * Display the specified resource.
     */
    public function show(Fiel $fiel)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Request $request, $id)
    {
        $companyId = session('active_company_id');

        $fiel = Fiel::with([
            'contacts',
            'addresses',
            'complementaryData',
            'tithe'
        ])
        ->where('company_id', $companyId)
        ->findOrFail($id);

        // Formatar dados para o formulário
        $avatarUrl = null;
        if ($fiel->avatar) {
            try {
                $avatarUrl = route('file', ['path' => $fiel->avatar]);
            } catch (\Exception $e) {
                $avatarUrl = Storage::disk('public')->url($fiel->avatar);
            }
        }

        $data = [
            'id' => $fiel->id,
            'nome_completo' => $fiel->nome_completo,
            'data_nascimento' => $fiel->data_nascimento ? $fiel->data_nascimento->format('d/m/Y') : null,
            'sexo' => $fiel->sexo,
            'cpf' => $fiel->cpf,
            'rg' => $fiel->rg,
            'status' => $fiel->status,
            'notifications' => $fiel->notifications ?? [],
            'avatar' => $avatarUrl,
            'profissao' => $fiel->complementaryData->profissao ?? null,
            'estado_civil' => $fiel->complementaryData->estado_civil ?? null,
            'telefone' => $fiel->contacts->where('tipo', 'telefone')->first()->valor ?? null,
            'telefone_secundario' => $fiel->contacts->where('tipo', 'telefone_secundario')->first()->valor ?? null,
            'email' => $fiel->contacts->where('tipo', 'email')->first()->valor ?? null,
            'dizimista' => $fiel->tithe && $fiel->tithe->dizimista ? true : false,
        ];

        // Dados do endereço principal
        $enderecoPrincipal = $fiel->addresses->where('pivot.tipo', 'principal')->first();
        if ($enderecoPrincipal) {
            $data['cep'] = $enderecoPrincipal->cep;
            $data['endereco'] = $enderecoPrincipal->rua;
            $data['bairro'] = $enderecoPrincipal->bairro;
            $data['cidade'] = $enderecoPrincipal->cidade;
            $data['estado'] = $enderecoPrincipal->uf;
        }

        if ($request->ajax() || $request->expectsJson()) {
            return response()->json([
                'success' => true,
                'data' => $data
            ]);
        }

        return view('app.cadastros.fieis.edit', compact('fiel'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        try {
            $companyId = session('active_company_id');

            // Buscar o fiel e verificar se pertence à empresa
            $fiel = Fiel::where('company_id', $companyId)->findOrFail($id);

            // Validação dos dados
            $validator = Validator::make($request->all(), [
                'nome_completo' => 'required|string|max:255',
                'cpf' => 'nullable|string|max:14|unique:fieis,cpf,' . $fiel->id,
                'rg' => 'nullable|string|max:20',
                'data_nascimento' => ['nullable', function ($attribute, $value, $fail) {
                    if ($value) {
                        $date = \Carbon\Carbon::createFromFormat('d/m/Y', $value);
                        if (!$date || $date->format('d/m/Y') !== $value) {
                            $date = \Carbon\Carbon::parse($value);
                            if (!$date) {
                                $fail('A data de nascimento deve estar no formato dd/mm/aaaa.');
                            }
                        }
                    }
                }],
                'sexo' => 'required|in:M,F,Outro',
                'avatar' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Erro de validação',
                    'errors' => $validator->errors()
                ], 422);
            }

            DB::beginTransaction();

            // Processar data de nascimento
            $dataNascimento = null;
            if ($request->data_nascimento) {
                try {
                    if (preg_match('/^\d{2}\/\d{2}\/\d{4}$/', $request->data_nascimento)) {
                        $dataNascimento = \Carbon\Carbon::createFromFormat('d/m/Y', $request->data_nascimento)->format('Y-m-d');
                    } else {
                        $dataNascimento = \Carbon\Carbon::parse($request->data_nascimento)->format('Y-m-d');
                    }
                } catch (\Exception $e) {
                    $dataNascimento = \Carbon\Carbon::parse($request->data_nascimento)->format('Y-m-d');
                }
            }

            // Atualizar dados básicos do fiel
            $fiel->nome_completo = $request->nome_completo;
            $fiel->cpf = $request->cpf;
            $fiel->rg = $request->rg;
            $fiel->data_nascimento = $dataNascimento;
            $fiel->sexo = $request->sexo;
            $fiel->notifications = json_encode($request->input('notifications', []));
            $fiel->status = $request->status ?? $fiel->status;
            $fiel->updated_by = auth()->user()->id;
            $fiel->updated_by_name = auth()->user()->name;

            // Upload do avatar se houver novo
            if ($request->hasFile('avatar')) {
                // Deletar avatar antigo se existir
                if ($fiel->avatar) {
                    Storage::disk('public')->delete($fiel->avatar);
                }
                $avatar = $request->file('avatar');
                $avatarPath = $avatar->store('avatars', 'public');
                $fiel->avatar = $avatarPath;
            }

            $fiel->save();

            // Atualizar contatos
            $fiel->contacts()->delete(); // Remove todos os contatos antigos
            if ($request->telefone) {
                FielContact::create([
                    'fiel_id' => $fiel->id,
                    'tipo' => 'telefone',
                    'valor' => $request->telefone,
                    'principal' => true,
                ]);
            }
            if ($request->telefone_secundario) {
                FielContact::create([
                    'fiel_id' => $fiel->id,
                    'tipo' => 'telefone_secundario',
                    'valor' => $request->telefone_secundario,
                    'principal' => false,
                ]);
            }
            if ($request->email) {
                FielContact::create([
                    'fiel_id' => $fiel->id,
                    'tipo' => 'email',
                    'valor' => $request->email,
                    'principal' => true,
                ]);
            }

            // Atualizar dados complementares
            $complementaryData = $fiel->complementaryData;
            if ($request->profissao || $request->estado_civil) {
                if ($complementaryData) {
                    $complementaryData->profissao = $request->profissao;
                    $complementaryData->estado_civil = $request->estado_civil;
                    $complementaryData->save();
                } else {
                    FielComplementaryData::create([
                        'fiel_id' => $fiel->id,
                        'profissao' => $request->profissao,
                        'estado_civil' => $request->estado_civil,
                    ]);
                }
            }

            // Atualizar endereço
            if ($request->cep || $request->endereco || $request->bairro || $request->cidade || $request->estado) {
                // Remove endereços antigos
                $fiel->addresses()->detach();

                $address = Address::firstOrCreate(
                    [
                        'cep' => $request->input('cep'),
                        'rua' => $request->input('endereco'),
                        'bairro' => $request->input('bairro'),
                        'cidade' => $request->input('cidade'),
                        'uf' => $request->input('estado')
                    ],
                    ['company_id' => $companyId]
                );
                $fiel->addresses()->attach($address->id, ['tipo' => 'principal']);
            }

            // Atualizar dados de dízimo
            if ($request->has('dizimista')) {
                $tithe = $fiel->tithe;
                if ($request->boolean('dizimista')) {
                    if ($tithe) {
                        $tithe->dizimista = true;
                        $tithe->save();
                    } else {
                        FielTithe::create([
                            'fiel_id' => $fiel->id,
                            'dizimista' => true,
                        ]);
                    }
                } else {
                    if ($tithe) {
                        $tithe->dizimista = false;
                        $tithe->save();
                    }
                }
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Fiel atualizado com sucesso!',
                'fiel' => $fiel
            ], 200);

        } catch (\Illuminate\Validation\ValidationException $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Erro de validação',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Ocorreu um erro ao atualizar o fiel: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Fiel $fiel)
    {
        //
    }

    /**
     * Retorna dados para os gráficos de fiéis
     */
    public function getChartData(Request $request)
    {
        $companyId = session('active_company_id');

        // Dados para gráfico de Faixa Etária
        $fieisComIdade = Fiel::where('company_id', $companyId)
            ->whereNotNull('data_nascimento')
            ->get();

        $faixasEtarias = [
            '0-17' => 0,
            '18-29' => 0,
            '30-44' => 0,
            '45-59' => 0,
            '60-74' => 0,
            '75+' => 0
        ];

        foreach ($fieisComIdade as $fiel) {
            $idade = \Carbon\Carbon::parse($fiel->data_nascimento)->age;
            if ($idade < 18) {
                $faixasEtarias['0-17']++;
            } elseif ($idade < 30) {
                $faixasEtarias['18-29']++;
            } elseif ($idade < 45) {
                $faixasEtarias['30-44']++;
            } elseif ($idade < 60) {
                $faixasEtarias['45-59']++;
            } elseif ($idade < 75) {
                $faixasEtarias['60-74']++;
            } else {
                $faixasEtarias['75+']++;
            }
        }

        // Remover faixas com zero
        $faixasEtarias = array_filter($faixasEtarias, function($value) {
            return $value > 0;
        });

        // Dados para gráfico de Estado Civil
        $estadosCivis = Fiel::where('company_id', $companyId)
            ->whereHas('complementaryData', function ($query) {
                $query->whereNotNull('estado_civil');
            })
            ->with('complementaryData')
            ->get()
            ->pluck('complementaryData.estado_civil')
            ->filter()
            ->countBy()
            ->toArray();

        // Dados para gráfico de Profissão (top 10)
        $profissoes = Fiel::where('company_id', $companyId)
            ->whereHas('complementaryData', function ($query) {
                $query->whereNotNull('profissao');
            })
            ->with('complementaryData')
            ->get()
            ->pluck('complementaryData.profissao')
            ->filter()
            ->countBy()
            ->sortDesc()
            ->take(10)
            ->toArray();

        return response()->json([
            'success' => true,
            'data' => [
                'faixas_etarias' => [
                    'labels' => array_keys($faixasEtarias),
                    'values' => array_values($faixasEtarias)
                ],
                'estados_civis' => [
                    'labels' => array_keys($estadosCivis),
                    'values' => array_values($estadosCivis)
                ],
                'profissoes' => [
                    'labels' => array_keys($profissoes),
                    'values' => array_values($profissoes)
                ]
            ]
        ], 200, [], JSON_UNESCAPED_UNICODE);
    }

    /**
     * Gera relatório PDF de fiéis com filtros
     */
    public function relatorioPdf(Request $request)
    {
        $companyId = session('active_company_id');
        $company = \App\Models\Company::with('addresses')->find($companyId);

        // Query base
        $query = Fiel::with(['company', 'contacts', 'addresses', 'tithe', 'complementaryData'])
            ->where('company_id', $companyId);

        $tituloRelatorio = 'Relatório de Fiéis';
        $subtitulos = [];

        // Filtro 1: Tipo de Registro (por enquanto só fiéis)
        $tipoRegistro = $request->input('tipo_registro', 'fieis');

        // Filtro 2: Dizimista
        $filtroDizimista = $request->input('filtro_dizimista', 'todos');
        switch ($filtroDizimista) {
            case 'sim':
                $query->whereHas('tithe', function ($q) {
                    $q->where('dizimista', true);
                });
                $subtitulos[] = 'Dizimistas';
                break;
            case 'nao':
                $query->where(function ($q) {
                    $q->whereDoesntHave('tithe')
                      ->orWhereHas('tithe', function ($subQ) {
                          $subQ->where('dizimista', false);
                      });
                });
                $subtitulos[] = 'Não Dizimistas';
                break;
        }

        // Filtro 3: Filtros por Data/Idade
        $filtroDataTipo = $request->input('filtro_data_tipo');

        switch ($filtroDataTipo) {
            case 'aniversariantes':
                $periodoAniversario = $request->input('periodo_aniversario');
                if ($periodoAniversario) {
                    // Período vem no formato "DD/MM até DD/MM"
                    $datas = explode(' até ', $periodoAniversario);
                    if (count($datas) == 2) {
                        $dataInicio = trim($datas[0]);
                        $dataFim = trim($datas[1]);

                        // Extrair dia e mês
                        list($diaInicio, $mesInicio) = explode('/', $dataInicio);
                        list($diaFim, $mesFim) = explode('/', $dataFim);

                        // Se for o mesmo mês
                        if ($mesInicio == $mesFim) {
                            $query->whereMonth('data_nascimento', $mesInicio)
                                  ->whereDay('data_nascimento', '>=', $diaInicio)
                                  ->whereDay('data_nascimento', '<=', $diaFim);
                        } else {
                            // Se cruzar meses
                            $query->where(function ($q) use ($diaInicio, $mesInicio, $diaFim, $mesFim) {
                                $q->where(function ($subQ) use ($diaInicio, $mesInicio) {
                                    $subQ->whereMonth('data_nascimento', $mesInicio)
                                         ->whereDay('data_nascimento', '>=', $diaInicio);
                                })->orWhere(function ($subQ) use ($diaFim, $mesFim) {
                                    $subQ->whereMonth('data_nascimento', $mesFim)
                                         ->whereDay('data_nascimento', '<=', $diaFim);
                                });
                            });
                        }

                        $subtitulos[] = "Aniversariantes de {$dataInicio} até {$dataFim}";
                    }
                }
                break;

            case 'idade':
                $idadeMinima = $request->input('idade_minima');
                $idadeMaxima = $request->input('idade_maxima');

                $hoje = \Carbon\Carbon::now();

                if ($idadeMaxima) {
                    $dataNascimentoMin = $hoje->copy()->subYears($idadeMaxima)->subDay();
                    $query->where('data_nascimento', '>=', $dataNascimentoMin);
                }

                if ($idadeMinima) {
                    $dataNascimentoMax = $hoje->copy()->subYears($idadeMinima);
                    $query->where('data_nascimento', '<=', $dataNascimentoMax);
                }

                if ($idadeMinima && $idadeMaxima) {
                    $subtitulos[] = "Idade entre {$idadeMinima} e {$idadeMaxima} anos";
                } elseif ($idadeMinima) {
                    $subtitulos[] = "Idade mínima: {$idadeMinima} anos";
                } elseif ($idadeMaxima) {
                    $subtitulos[] = "Idade máxima: {$idadeMaxima} anos";
                }
                break;
        }

        // Filtros complementares
        $sexo = $request->input('sexo');
        if ($sexo) {
            $query->where('sexo', $sexo);
            $sexoTexto = $sexo === 'M' ? 'Masculino' : 'Feminino';
            $subtitulos[] = "Sexo: {$sexoTexto}";
        }

        $estadoCivil = $request->input('estado_civil');
        if ($estadoCivil) {
            $query->whereHas('complementaryData', function ($q) use ($estadoCivil) {
                $q->where('estado_civil', $estadoCivil);
            });
            $subtitulos[] = "Estado Civil: {$estadoCivil}";
        }

        // Montar título final
        if (!empty($subtitulos)) {
            $tituloRelatorio .= ' - ' . implode(' | ', $subtitulos);
        }

        // Aplicar ordenação
        $ordenarPor = $request->input('ordenar_por', 'nome');
        switch ($ordenarPor) {
            case 'nome':
                $query->orderBy('nome_completo');
                break;
            case 'data_nascimento':
                $query->orderBy('data_nascimento');
                break;
            case 'cpf':
                $query->orderBy('cpf');
                break;
        }

        $fieis = $query->get();

        // Escolher o layout do relatório
        $layoutRelatorio = $request->input('layout_relatorio', 'resumido');
        $viewName = $layoutRelatorio === 'detalhado'
            ? 'app.relatorios.fieis.fieis_detalhado_pdf'
            : 'app.relatorios.fieis.fieis_pdf';

        // Gerar PDF usando Browsershot
        $html = view($viewName, compact(
            'fieis',
            'company',
            'tituloRelatorio'
        ))->render();

        try {
            $browsershot = \App\Helpers\BrowsershotHelper::configureChromePath(
                \Spatie\Browsershot\Browsershot::html($html)
                    ->format('A4')
                    ->showBackground()
                    ->margins(8, 8, 10, 8)
                    ->waitUntilNetworkIdle()
            );

            // Aplicar orientação baseada no layout
            if ($layoutRelatorio === 'detalhado') {
                $browsershot->portrait();
            } else {
                $browsershot->landscape();
            }

            $pdf = $browsershot->pdf();

            return response($pdf, 200, [
                'Content-Type' => 'application/pdf',
                'Content-Disposition' => 'inline; filename="relatorio-fieis.pdf"',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erro ao gerar PDF: ' . $e->getMessage()
            ], 500);
        }
    }
}
