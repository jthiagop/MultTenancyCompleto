<?php

namespace App\Http\Controllers\App;

use App\Http\Controllers\Controller;
use App\Models\Fiel;
use App\Models\Address;
use App\Models\FielContact;
use App\Models\FielComplementaryData;
use App\Helpers\BrowsershotHelper;
use App\Models\FielTithe;
use App\Models\User;
use App\Services\Fieis\CarteirinhaFielService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Spatie\Browsershot\Browsershot;


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
                    $avatar = $row->avatar ? route('file', ['path' => $row->avatar]) : '/tenancy/assets/media/png/perfil.svg';

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
     * GET /api/fieis
     * Listagem paginada para o painel React.
     *
     * Suporta: search, sort_by/sort_dir, page/per_page, status, sexo, dizimista.
     * Retorna formato `{ data, total, per_page, current_page, last_page }`.
     */
    public function apiList(Request $request)
    {
        $companyId = session('active_company_id');

        if (! $companyId) {
            return response()->json([
                'data' => [], 'total' => 0, 'per_page' => 20, 'current_page' => 1, 'last_page' => 1,
            ]);
        }

        $query = Fiel::with(['contacts', 'addresses', 'tithe'])
            ->where('company_id', $companyId);

        if ($search = trim((string) $request->input('search'))) {
            $digitsOnly = preg_replace('/\D/', '', $search);
            $query->where(function ($q) use ($search, $digitsOnly) {
                $q->where('nome_completo', 'like', "%{$search}%")
                  ->orWhere('cpf', 'like', "%{$search}%")
                  ->orWhere('rg', 'like', "%{$search}%")
                  ->orWhereHas('contacts', fn ($c) => $c->where('valor', 'like', "%{$search}%"));

                if ($digitsOnly !== '' && strlen($digitsOnly) >= 3) {
                    $q->orWhere('cpf', 'like', '%' . $digitsOnly . '%');
                }

                $q->orWhereHas('tithe', fn ($t) => $t->where('codigo', 'like', '%' . $search . '%'));
            });
        }

        if ($status = $request->input('status')) {
            $query->where('status', $status);
        }

        // Sexo — tratado no bloco de filtros avançados abaixo (suporta valor único e lista CSV)

        if ($request->filled('dizimista')) {
            $isDizimista = filter_var($request->input('dizimista'), FILTER_VALIDATE_BOOLEAN);
            if ($isDizimista) {
                $query->whereHas('tithe', fn ($q) => $q->where('dizimista', true));
            } else {
                $query->where(function ($q) {
                    $q->whereDoesntHave('tithe')
                      ->orWhereHas('tithe', fn ($sub) => $sub->where('dizimista', false));
                });
            }
        }

        // ── Filtros avançados ──────────────────────────────────────────────────

        // Faixa de idade (calculada via data_nascimento)
        if ($request->filled('idade_min')) {
            $minAge = (int) $request->input('idade_min');
            $maxDate = \Carbon\Carbon::now()->subYears($minAge)->format('Y-m-d');
            $query->where('data_nascimento', '<=', $maxDate);
        }
        if ($request->filled('idade_max')) {
            $maxAge = (int) $request->input('idade_max');
            $minDate = \Carbon\Carbon::now()->subYears($maxAge + 1)->addDay()->format('Y-m-d');
            $query->where('data_nascimento', '>=', $minDate);
        }

        // Cidade (via endereço vinculado)
        if ($cidade = trim((string) $request->input('cidade'))) {
            $query->whereHas('addresses', fn ($q) => $q->where('cidade', 'like', "%{$cidade}%"));
        }

        // Estado civil (múltiplo, via dados complementares)
        if ($request->filled('estado_civil')) {
            $estadosCivis = array_filter(array_map('trim', explode(',', $request->input('estado_civil'))));
            if (! empty($estadosCivis)) {
                $query->whereHas('complementaryData', fn ($q) => $q->whereIn('estado_civil', $estadosCivis));
            }
        }

        // Situação (múltipla — complementa o filtro de status da aba)
        if ($request->filled('situacao')) {
            $situacoes = array_filter(array_map('trim', explode(',', $request->input('situacao'))));
            if (! empty($situacoes)) {
                $query->whereIn('status', $situacoes);
            }
        }

        // Faixa de data de nascimento
        if ($nascimentoDe = $request->input('nascimento_de')) {
            $query->where('data_nascimento', '>=', $nascimentoDe);
        }
        if ($nascimentoAte = $request->input('nascimento_ate')) {
            $query->where('data_nascimento', '<=', $nascimentoAte);
        }

        // Sexo múltiplo (sobrepõe o filtro simples da aba quando vier como lista CSV)
        if ($request->filled('sexo')) {
            $sexoRaw = $request->input('sexo');
            if (str_contains($sexoRaw, ',')) {
                $sexos = array_filter(array_map('trim', explode(',', $sexoRaw)));
                if (! empty($sexos)) {
                    $query->whereIn('sexo', $sexos);
                }
            } else {
                // filtro simples da aba (valor único)
                $query->where('sexo', $sexoRaw);
            }
        }

        $sortBy  = $request->input('sort_by', 'nome_completo');
        $sortDir = $request->input('sort_dir', 'asc') === 'desc' ? 'desc' : 'asc';
        $allowedSort = ['nome_completo', 'data_nascimento', 'cpf', 'sexo', 'status', 'created_at'];
        if (! in_array($sortBy, $allowedSort, true)) {
            $sortBy = 'nome_completo';
        }
        $query->orderBy($sortBy, $sortDir);

        $perPage = min(max((int) $request->input('per_page', 20), 5), 100);
        $paginated = $query->paginate($perPage);

        $data = $paginated->getCollection()->map(function (Fiel $fiel) {
            // Avatar
            $avatarUrl = null;
            if ($fiel->avatar) {
                try {
                    $avatarUrl = route('file', ['path' => $fiel->avatar]);
                } catch (\Throwable) {
                    $avatarUrl = Storage::disk('public')->url($fiel->avatar);
                }
            }

            // Contatos: telefone (telefone ou whatsapp) + email
            $telefoneContact = $fiel->contacts->first(fn ($c) => in_array($c->tipo, ['telefone', 'whatsapp', 'telefone_secundario'], true));
            $emailContact = $fiel->contacts->first(fn ($c) => $c->tipo === 'email');

            // Endereço principal — cidade/uf
            $enderecoPrincipal = $fiel->addresses->first(fn ($a) => ($a->pivot->tipo ?? null) === 'principal')
                ?? $fiel->addresses->first();
            $cidadeUf = null;
            if ($enderecoPrincipal) {
                $cidadeUf = trim(implode(' / ', array_filter([$enderecoPrincipal->cidade, $enderecoPrincipal->uf])));
            }

            return [
                'id'                       => $fiel->id,
                'nome_completo'            => $fiel->nome_completo,
                'avatar_url'               => $avatarUrl,
                'sexo'                     => $fiel->sexo,
                'cpf'                      => $fiel->cpf,
                'rg'                       => $fiel->rg,
                'data_nascimento'          => $fiel->data_nascimento?->format('Y-m-d'),
                'data_nascimento_formatted'=> $fiel->data_nascimento?->format('d/m/Y'),
                'idade'                    => $fiel->data_nascimento ? (int) $fiel->data_nascimento->age : null,
                'telefone'                 => $telefoneContact?->valor,
                'telefone_is_whatsapp'     => $telefoneContact?->tipo === 'whatsapp',
                'email'                    => $emailContact?->valor,
                'cidade_uf'                => $cidadeUf ?: null,
                'dizimista'                => (bool) ($fiel->tithe?->dizimista ?? false),
                'codigo_dizimista'         => $fiel->tithe?->codigo,
                'status'                   => $fiel->status ?? 'Ativo',
                'created_at_formatted'     => $fiel->created_at?->translatedFormat('d/m/Y'),
            ];
        });

        // ── Stats globais (sem os filtros ativos, exceto search) ──────────────
        // Serve para as abas do FieisStatsBar no frontend.
        $statsBase = Fiel::where('company_id', $companyId);
        if ($search) {
            $digitsOnly = preg_replace('/\D/', '', $search);
            $statsBase->where(function ($q) use ($search, $digitsOnly) {
                $q->where('nome_completo', 'like', "%{$search}%")
                  ->orWhere('cpf', 'like', "%{$search}%")
                  ->orWhere('rg', 'like', "%{$search}%")
                  ->orWhereHas('contacts', fn ($c) => $c->where('valor', 'like', "%{$search}%"));
                if ($digitsOnly !== '' && strlen($digitsOnly) >= 3) {
                    $q->orWhere('cpf', 'like', '%' . $digitsOnly . '%');
                }
                $q->orWhereHas('tithe', fn ($t) => $t->where('codigo', 'like', '%' . $search . '%'));
            });
        }

        $total      = (clone $statsBase)->count();
        $masculino  = (clone $statsBase)->where('sexo', 'M')->count();
        $feminino   = (clone $statsBase)->where('sexo', 'F')->count();
        $dizimista  = (clone $statsBase)->whereHas('tithe', fn ($q) => $q->where('dizimista', true))->count();
        $ativos     = (clone $statsBase)->where('status', 'Ativo')->count();

        return response()->json([
            'data'         => $data,
            'total'        => $paginated->total(),
            'per_page'     => $paginated->perPage(),
            'current_page' => $paginated->currentPage(),
            'last_page'    => $paginated->lastPage(),
            'stats' => [
                'total'     => $total,
                'masculino' => $masculino,
                'feminino'  => $feminino,
                'dizimista' => $dizimista,
                'ativos'    => $ativos,
            ],
        ]);
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
     *
     * Aceita o formato moderno do React (phones[] com is_whatsapp, codigo_dizimista,
     * observacoes, status enum) e mantém compatibilidade com o formato Blade legado
     * (telefone, telefone_secundario, profissao).
     */
    public function store(Request $request)
    {
        try {
            $companyId = session('active_company_id');

            // Quando `require_cpf=1` (fluxo de quick-create do drawer de Dízimo),
            // o CPF passa a ser obrigatório e a regra de unicidade é restrita à
            // company ativa para evitar colisão entre tenants.
            $requireCpf = $request->boolean('require_cpf');

            $cpfRule = $requireCpf
                ? ['required', 'string', 'max:14',
                   'unique:fieis,cpf,NULL,id,company_id,' . (int) $companyId]
                : ['nullable', 'string', 'max:14',
                   'unique:fieis,cpf,NULL,id,company_id,' . (int) $companyId];

            // Validação dos dados
            $validator = Validator::make($request->all(), [
                'nome_completo' => 'required|string|max:255',
                'cpf' => $cpfRule,
                'rg' => 'nullable|string|max:20',
                'data_nascimento' => ['nullable', function ($attribute, $value, $fail) {
                    if ($value) {
                        $iso = preg_match('/^\d{4}-\d{2}-\d{2}$/', $value);
                        $br = preg_match('/^\d{2}\/\d{2}\/\d{4}$/', $value);
                        if (! $iso && ! $br) {
                            $fail('A data de nascimento deve estar no formato dd/mm/aaaa.');
                        }
                    }
                }],
                'sexo' => 'nullable|in:M,F,Outro',
                'avatar' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
                'email' => 'nullable|email|max:255',
                'status' => 'nullable|in:Ativo,Inativo,Falecido,Mudou-se',
                'phones' => 'nullable|array|max:5',
                'phones.*.numero' => 'nullable|string|max:25',
                'phones.*.is_whatsapp' => 'nullable',
                'codigo_dizimista' => 'nullable|string|max:50',
                'observacoes'      => 'nullable|string|max:2000',
                'estado_civil'     => 'nullable|string|max:50',
                'profissao'        => 'nullable|string|max:150',
                'nacionalidade'    => 'nullable|string|max:100',
                'natural'          => 'nullable|string|max:150',
                'uf_natural'       => 'nullable|string|max:2',
                'titulo_eleitor'   => 'nullable|string|max:20',
                'zona'             => 'nullable|string|max:10',
                'secao'            => 'nullable|string|max:10',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Erro de validação',
                    'errors' => $validator->errors()
                ], 422);
            }

            DB::beginTransaction();

            // Processar data de nascimento (aceita formato brasileiro d/m/Y e ISO)
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

            // Dados básicos do fiel
            $fielData = [
                'company_id' => $companyId,
                'nome_completo' => $request->nome_completo,
                'cpf' => $request->cpf,
                'rg' => $request->rg,
                'data_nascimento' => $dataNascimento,
                'sexo' => $request->input('sexo', 'M'),
                'notifications' => json_encode($request->input('notifications', [])),
                'status' => $request->input('status', 'Ativo'),
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

            // ── Contatos ────────────────────────────────────────────────
            // Formato moderno: phones[] com is_whatsapp. Cada item vira:
            //   - 1 contato `telefone` (ou `whatsapp` quando flag ligada)
            // Formato legado: telefone / telefone_secundario (ainda aceito).
            $this->saveFielContacts($fiel, $request);

            // Salvar dados complementares (todos os campos do modelo)
            $complementarFields = [
                'profissao', 'estado_civil', 'nacionalidade',
                'natural', 'uf_natural', 'titulo_eleitor', 'zona', 'secao', 'observacoes',
            ];
            $hasComplementary = collect($complementarFields)->some(fn ($f) => $request->filled($f));
            if ($hasComplementary) {
                $complementarData = ['fiel_id' => $fiel->id];
                foreach ($complementarFields as $field) {
                    $complementarData[$field] = $request->input($field) ?: null;
                }
                FielComplementaryData::create($complementarData);
            }

            // Salvar endereço (aceita `endereco`/`logradouro`, `estado`/`uf` e `numero`)
            $this->saveFielAddress($fiel, $request, $companyId);

            // Salvar dados de dízimo (gera código D-XXXX automaticamente quando não informado)
            if ($request->has('dizimista') && $request->boolean('dizimista')) {
                $tithe = FielTithe::create([
                    'fiel_id'   => $fiel->id,
                    'dizimista' => true,
                ]);

                app(CarteirinhaFielService::class)->ensureCodigo(
                    $tithe,
                    $request->input('codigo_dizimista'),
                    $companyId,
                );
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
     * Persiste contatos do fiel a partir do request.
     *
     * Suporta dois formatos:
     *  1. Moderno (React): `phones[]` com `numero` + `is_whatsapp`.
     *  2. Legado (Blade): `telefone` (principal), `telefone_secundario`.
     *
     * Quando `phones[]` está presente, ele é a fonte de verdade — os campos
     * legados são ignorados para evitar duplicidade. O e-mail (`email`) é
     * sempre tratado de forma independente.
     *
     * @param  bool  $replaceExisting  Se true, remove antes telefone/whatsapp/secundário/e-mail
     *                                deste fiel (usado em atualização via React). Evita duplicatas.
     */
    protected function saveFielContacts(Fiel $fiel, Request $request, bool $replaceExisting = false): void
    {
        if ($replaceExisting) {
            $fiel->contacts()
                ->whereIn('tipo', ['telefone', 'whatsapp', 'telefone_secundario', 'email'])
                ->delete();
        }

        $phones = $request->input('phones');

        if (is_array($phones) && ! empty($phones)) {
            $first = true;
            foreach ($phones as $phone) {
                $numero = trim((string) ($phone['numero'] ?? ''));
                if ($numero === '') {
                    continue;
                }
                $isWhats = filter_var($phone['is_whatsapp'] ?? false, FILTER_VALIDATE_BOOLEAN);
                FielContact::create([
                    'fiel_id'   => $fiel->id,
                    'tipo'      => $isWhats ? 'whatsapp' : 'telefone',
                    'valor'     => $numero,
                    'principal' => $first,
                ]);
                $first = false;
            }
        } else {
            // Legado: telefone / telefone_secundario
            if ($request->filled('telefone')) {
                FielContact::create([
                    'fiel_id'   => $fiel->id,
                    'tipo'      => 'telefone',
                    'valor'     => $request->input('telefone'),
                    'principal' => true,
                ]);
            }
            if ($request->filled('telefone_secundario')) {
                FielContact::create([
                    'fiel_id'   => $fiel->id,
                    'tipo'      => 'telefone_secundario',
                    'valor'     => $request->input('telefone_secundario'),
                    'principal' => false,
                ]);
            }
        }

        if ($request->filled('email')) {
            FielContact::create([
                'fiel_id'   => $fiel->id,
                'tipo'      => 'email',
                'valor'     => $request->input('email'),
                'principal' => true,
            ]);
        }
    }

    /**
     * Persiste o endereço principal do fiel, aceitando aliases moderno/legado.
     *  - logradouro|endereco → Address.rua
     *  - uf|estado           → Address.uf
     *  - numero              → Address.numero (se a coluna existir)
     */
    protected function saveFielAddress(Fiel $fiel, Request $request, $companyId): void
    {
        $rua    = $request->input('endereco') ?? $request->input('logradouro');
        $uf     = $request->input('estado') ?? $request->input('uf');
        $cep    = $request->input('cep');
        $bairro = $request->input('bairro');
        $cidade = $request->input('cidade');
        $numero = $request->input('numero');

        if (! ($cep || $rua || $bairro || $cidade || $uf || $numero)) {
            return;
        }

        $payload = [
            'company_id' => $companyId,
            'cep'        => $cep,
            'rua'        => $rua,
            'bairro'     => $bairro,
            'cidade'     => $cidade,
            'uf'         => $uf,
        ];

        if ($numero && \Illuminate\Support\Facades\Schema::hasColumn('addresses', 'numero')) {
            $payload['numero'] = $numero;
        }

        $address = Address::create($payload);
        $fiel->addresses()->attach($address->id, ['tipo' => 'principal']);
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
     * Retorna os dados de um fiel para o formulário React.
     */
    public function showReact(Request $request, $id)
    {
        try {
            $companyId = session('active_company_id');

            $fiel = Fiel::with(['contacts', 'addresses', 'complementaryData', 'tithe'])
                ->where('company_id', $companyId)
                ->findOrFail($id);

            $avatarUrl = null;
            if ($fiel->avatar) {
                try {
                    $avatarUrl = route('file', ['path' => $fiel->avatar]);
                } catch (\Exception $e) {
                    $avatarUrl = Storage::disk('public')->url($fiel->avatar);
                }
            }

            // Monta array de phones com is_whatsapp (ordem estável: principal primeiro, depois id)
            $phoneRows = $fiel->contacts
                ->filter(fn ($c) => in_array($c->tipo, ['telefone', 'whatsapp', 'telefone_secundario'], true))
                ->values();

            $phones = $phoneRows
                ->sort(function ($a, $b) {
                    $pa = (int) (bool) ($a->principal ?? false);
                    $pb = (int) (bool) ($b->principal ?? false);
                    if ($pa !== $pb) {
                        return $pb <=> $pa;
                    }

                    return ($a->id ?? 0) <=> ($b->id ?? 0);
                })
                ->map(function ($c) {
                    return [
                        'numero'        => $c->valor,
                        'is_whatsapp'   => $c->tipo === 'whatsapp',
                    ];
                })
                ->values()
                ->toArray();

            if (empty($phones)) {
                $phones = [['numero' => '', 'is_whatsapp' => true]];
            }

            $email = $fiel->contacts->where('tipo', 'email')->first()->valor ?? '';

            $comp = $fiel->complementaryData;
            $tithe = $fiel->tithe;

            // Endereço principal
            $address = ['cep' => '', 'logradouro' => '', 'numero' => '', 'bairro' => '', 'cidade' => '', 'uf' => ''];
            $enderecoPrincipal = $fiel->addresses->first();
            if ($enderecoPrincipal) {
                $address = [
                    'cep'       => $enderecoPrincipal->cep ?? '',
                    'logradouro' => $enderecoPrincipal->rua ?? '',
                    'numero'    => $enderecoPrincipal->pivot->numero ?? ($enderecoPrincipal->numero ?? ''),
                    'bairro'    => $enderecoPrincipal->bairro ?? '',
                    'cidade'    => $enderecoPrincipal->cidade ?? '',
                    'uf'        => $enderecoPrincipal->uf ?? '',
                ];
            }

            return response()->json([
                'success' => true,
                'data' => [
                    'id'               => $fiel->id,
                    'nome_completo'    => $fiel->nome_completo,
                    'data_nascimento'  => $fiel->data_nascimento ? $fiel->data_nascimento->format('Y-m-d') : '',
                    'cpf'              => $fiel->cpf ?? '',
                    'rg'               => $fiel->rg ?? '',
                    'sexo'             => $fiel->sexo,
                    'status'           => $fiel->status ?? 'Ativo',
                    'avatar_url'       => $avatarUrl,
                    'phones'           => $phones,
                    'email'            => $email,
                    'profissao'        => $comp->profissao ?? '',
                    'estado_civil'     => $comp->estado_civil ?? '',
                    'nacionalidade'    => $comp->nacionalidade ?? '',
                    'natural'          => $comp->natural ?? '',
                    'uf_natural'       => $comp->uf_natural ?? '',
                    'titulo_eleitor'   => $comp->titulo_eleitor ?? '',
                    'zona'             => $comp->zona ?? '',
                    'secao'            => $comp->secao ?? '',
                    'observacoes'      => $comp->observacoes ?? '',
                    'dizimista'        => $tithe ? (bool) $tithe->dizimista : false,
                    'codigo_dizimista' => $tithe->codigo ?? '',
                    'address'          => $address,
                ],
            ]);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json(['message' => 'Fiel não encontrado.'], 404);
        } catch (\Throwable $e) {
            \Log::error('showReact Fiel: ' . $e->getMessage());
            return response()->json(['message' => 'Erro ao carregar dados do fiel.'], 500);
        }
    }

    /**
     * Atualiza um fiel via React (sessão web), aceitando todos os campos do novo formulário.
     */
    public function updateReact(Request $request, $id)
    {
        try {
            $companyId = session('active_company_id');

            $fiel = Fiel::where('company_id', $companyId)->findOrFail($id);

            $validator = Validator::make($request->all(), [
                'nome_completo'    => 'required|string|max:255',
                'cpf'              => 'nullable|string|max:14|unique:fieis,cpf,' . $fiel->id,
                'rg'               => 'nullable|string|max:20',
                'data_nascimento'  => 'nullable|string',
                'sexo'             => 'nullable|in:M,F,Outro',
                'avatar'           => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
                'status'           => 'nullable|in:Ativo,Inativo,Falecido,Mudou-se',
                'email'            => 'nullable|email|max:255',
                'phones'           => 'nullable|array|max:3',
                'phones.*.numero'  => 'nullable|string|max:20',
                'phones.*.is_whatsapp' => 'nullable|boolean',
                'estado_civil'     => 'nullable|string|max:100',
                'profissao'        => 'nullable|string|max:150',
                'nacionalidade'    => 'nullable|string|max:100',
                'natural'          => 'nullable|string|max:150',
                'uf_natural'       => 'nullable|string|max:2',
                'titulo_eleitor'   => 'nullable|string|max:20',
                'zona'             => 'nullable|string|max:10',
                'secao'            => 'nullable|string|max:10',
                'observacoes'      => 'nullable|string|max:2000',
                'dizimista'        => 'nullable|boolean',
                'codigo_dizimista' => 'nullable|string|max:50',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Erro de validação',
                    'errors'  => $validator->errors(),
                ], 422);
            }

            DB::transaction(function () use ($request, $fiel, $companyId) {
                // ── Dados básicos ──────────────────────────────────────────
                $dataNascimento = null;
                if ($request->data_nascimento) {
                    try {
                        $dataNascimento = \Carbon\Carbon::parse($request->data_nascimento)->format('Y-m-d');
                    } catch (\Exception $e) {}
                }

                $fiel->nome_completo   = $request->nome_completo;
                $fiel->cpf             = $request->cpf ?: null;
                $fiel->rg              = $request->rg ?: null;
                $fiel->data_nascimento = $dataNascimento;
                $fiel->sexo            = $request->sexo;
                $fiel->status          = $request->status ?? $fiel->status;

                if ($request->hasFile('avatar')) {
                    if ($fiel->avatar) {
                        Storage::disk('public')->delete($fiel->avatar);
                    }
                    $fiel->avatar = $request->file('avatar')->store('avatars', 'public');
                }

                $fiel->save();

                // ── Contatos (substitui conjunto anterior — evita duplicar linhas a cada save) ──
                $this->saveFielContacts($fiel, $request, true);

                // ── Endereço ───────────────────────────────────────────────
                $this->saveFielAddress($fiel, $request, $companyId);

                // ── Complementar ───────────────────────────────────────────
                $comp = $fiel->complementaryData;
                $complementarData = [
                    'profissao'      => $request->input('profissao') ?: null,
                    'estado_civil'   => $request->input('estado_civil') ?: null,
                    'nacionalidade'  => $request->input('nacionalidade') ?: null,
                    'natural'        => $request->input('natural') ?: null,
                    'uf_natural'     => $request->input('uf_natural') ?: null,
                    'titulo_eleitor' => $request->input('titulo_eleitor') ?: null,
                    'zona'           => $request->input('zona') ?: null,
                    'secao'          => $request->input('secao') ?: null,
                    'observacoes'    => $request->input('observacoes') ?: null,
                ];
                if ($comp) {
                    $comp->fill($complementarData)->save();
                } else {
                    FielComplementaryData::create(array_merge(['fiel_id' => $fiel->id], $complementarData));
                }

                // ── Dízimo ─────────────────────────────────────────────────
                $isDizimista = $request->boolean('dizimista');
                $tithe = $fiel->tithe;

                if (! $tithe && $isDizimista) {
                    $tithe = FielTithe::create([
                        'fiel_id'   => $fiel->id,
                        'dizimista' => true,
                    ]);
                } elseif ($tithe) {
                    $tithe->dizimista = $isDizimista;
                    $tithe->save();
                }

                // Gera/atualiza o código de dizimista somente quando ativo
                if ($tithe && $isDizimista) {
                    app(CarteirinhaFielService::class)->ensureCodigo(
                        $tithe,
                        $request->input('codigo_dizimista'),
                        $companyId,
                    );
                }
            });

            return response()->json([
                'success' => true,
                'message' => 'Fiel atualizado com sucesso!',
            ]);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json(['message' => 'Fiel não encontrado.'], 404);
        } catch (\Throwable $e) {
            \Log::error('updateReact Fiel: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Erro ao atualizar o fiel: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Retorna os dados da carteirinha (SVG do QR + Code128) para preview no React.
     * GET /api/cadastros/fieis/{id}/carteirinha
     */
    public function carteirinhaData(Request $request, $id, CarteirinhaFielService $carteirinha)
    {
        $companyId = session('active_company_id');
        Log::info('[carteirinha] carteirinhaData iniciado', [
            'fiel_id'    => $id,
            'company_id' => $companyId,
            'user_id'    => Auth::id(),
        ]);

        try {
            $fiel = Fiel::with(['tithe', 'company'])
                ->where('company_id', $companyId)
                ->findOrFail($id);

            if (! $fiel->tithe || ! $fiel->tithe->dizimista) {
                Log::warning('[carteirinha] carteirinhaData — fiel não é dizimista ou sem registro em fiel_tithe', [
                    'fiel_id'     => $fiel->id,
                    'company_id'  => $companyId,
                    'tem_tithe'   => (bool) $fiel->tithe,
                    'tithe_id'    => $fiel->tithe?->id,
                    'dizimista'   => $fiel->tithe?->dizimista,
                ]);

                return response()->json([
                    'success' => false,
                    'message' => 'Este fiel não está marcado como dizimista.',
                ], 404);
            }

            Log::debug('[carteirinha] carteirinhaData — ensureCodigo', [
                'fiel_id'       => $fiel->id,
                'tithe_id'      => $fiel->tithe->id,
                'codigo_atual'  => $fiel->tithe->codigo,
            ]);

            $codigo = $carteirinha->ensureCodigo($fiel->tithe, $fiel->tithe->codigo, (int) $companyId);

            // QR payload — enviado ao frontend para gerar o QR code client-side
            // (biblioteca JS qrcode). Os SVGs do QR e barcode não são mais gerados
            // aqui: o frontend usa qrcode + bwip-js para produzir imagens PNG e
            // gerar o PDF com @react-pdf/renderer, sem depender do Browsershot.
            $payload = $carteirinha->payloadQr($fiel, $codigo);

            Log::info('[carteirinha] carteirinhaData concluído', [
                'fiel_id'   => $fiel->id,
                'codigo'    => $codigo,
                'qr_payload'=> $payload,
            ]);

            $companyAvatar = $fiel->company->avatar ?? null;

            return response()->json([
                'success' => true,
                'data'    => [
                    'codigo'     => $codigo,
                    'qr_payload' => $payload,
                    'fiel' => [
                        'id'            => $fiel->id,
                        'nome_completo' => $fiel->nome_completo,
                        'avatar_url'    => $fiel->avatar
                            ? Storage::disk('public')->url($fiel->avatar)
                            : null,
                    ],
                    'company' => [
                        'nome'     => $fiel->company->nome ?? ($fiel->company->name ?? null),
                        'logo_url' => $companyAvatar
                            ? Storage::disk('public')->url($companyAvatar)
                            : null,
                    ],
                ],
            ]);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            Log::warning('[carteirinha] carteirinhaData — fiel não encontrado', [
                'fiel_id'    => $id,
                'company_id' => $companyId,
            ]);

            return response()->json(['success' => false, 'message' => 'Fiel não encontrado.'], 404);
        } catch (\Throwable $e) {
            Log::error('[carteirinha] carteirinhaData falhou', [
                'fiel_id'    => $id,
                'company_id' => $companyId,
                'exception'  => $e::class,
                'message'    => $e->getMessage(),
                'file'       => $e->getFile(),
                'line'       => $e->getLine(),
                'trace'      => $e->getTraceAsString(),
            ]);

            return response()->json(['success' => false, 'message' => 'Erro ao gerar carteirinha.'], 500);
        }
    }

    /**
     * Gera um PDF em lote com várias carteirinhas para impressão duplex em
     * massa.
     *
     * Layout: A4 retrato, grade 2x2 (4 cartões A6 por página).
     *
     * Para alinhar a impressão duplex, alternamos páginas:
     *   página N (ímpar) — frente dos próximos 4 fiéis (offsets 0..3)
     *   página N+1 (par) — verso dos mesmos 4 fiéis (mesmas posições)
     *
     * Assim, ao imprimir frente/verso, os versos caem nas costas certas
     * dos cartões.
     *
     * GET /relatorios/fieis/carteirinhas/pdf?ids=1,2,3,...
     */
    public function carteirinhasLotePdf(Request $request, CarteirinhaFielService $carteirinha)
    {
        // Lote pode ser pesado: várias gerações de SVG + pipeline Browsershot.
        @set_time_limit(180);
        @ini_set('max_execution_time', '180');

        $companyId = session('active_company_id');
        $idsParam  = (string) $request->query('ids', '');
        $ids = collect(explode(',', $idsParam))
            ->map(fn ($v) => (int) trim($v))
            ->filter(fn ($v) => $v > 0)
            ->unique()
            ->values()
            ->all();

        Log::info('[carteirinha] carteirinhasLotePdf iniciado', [
            'company_id'   => $companyId,
            'ids_count'    => count($ids),
            'user_id'      => Auth::id(),
        ]);

        if (empty($ids)) {
            abort(422, 'Informe ao menos um fiel.');
        }

        // Limite de segurança para evitar timeout / memória descontrolada.
        if (count($ids) > 60) {
            abort(422, 'Selecione no máximo 60 fiéis por lote.');
        }

        try {
            $fieis = Fiel::with(['tithe', 'company'])
                ->where('company_id', $companyId)
                ->whereIn('id', $ids)
                ->whereHas('tithe', fn ($q) => $q->where('dizimista', true))
                ->orderBy('nome_completo')
                ->get();

            if ($fieis->isEmpty()) {
                Log::warning('[carteirinha] carteirinhasLotePdf — nenhum fiel dizimista encontrado', [
                    'company_id' => $companyId,
                    'ids'        => $ids,
                ]);
                abort(404, 'Nenhum fiel dizimista encontrado para os IDs informados.');
            }

            $companyAvatar  = optional($fieis->first()->company)->avatar;
            $organismo = $fieis->first()->company->nome
                ?? ($fieis->first()->company->name ?? '—');

            // Converte um storage path (disco 'public') para uma data URL base64
            // inline, evitando qualquer requisição HTTP durante a renderização
            // pelo Browsershot (que carrega o HTML via file://).
            $toDataUrl = static function (?string $storagePath): ?string {
                if (!$storagePath) {
                    return null;
                }
                try {
                    $disk     = Storage::disk('public');
                    $contents = $disk->get($storagePath);
                    if (!$contents) {
                        return null;
                    }
                    $mime = $disk->mimeType($storagePath) ?: 'image/jpeg';
                    return 'data:' . $mime . ';base64,' . base64_encode($contents);
                } catch (\Throwable) {
                    return null;
                }
            };

            $companyLogoDataUrl = $toDataUrl($companyAvatar);

            // Para cada fiel: garante código, gera QR e barcode.
            $cards = [];
            foreach ($fieis as $fiel) {
                $codigo = $carteirinha->ensureCodigo(
                    $fiel->tithe,
                    $fiel->tithe->codigo,
                    (int) $companyId
                );
                $payload = $carteirinha->payloadQr($fiel, $codigo);
                $cards[] = [
                    'fiel'          => $fiel,
                    'codigo'        => $codigo,
                    'qrSvg'         => $carteirinha->qrCodeSvg($payload, 160, 1),
                    'barSvg'        => $carteirinha->code128Svg($codigo, 280, 50, true),
                    'avatarDataUrl' => $toDataUrl($fiel->avatar),
                ];
            }

            $html = view('app.fieis.carteirinhas-lote-pdf', [
                'cards'              => $cards,
                'organismo'          => $organismo,
                'companyLogoDataUrl' => $companyLogoDataUrl,
                'ano'                => (int) date('Y'),
            ])->render();

            Log::info('[carteirinha] carteirinhasLotePdf — HTML renderizado', [
                'cards_count' => count($cards),
                'html_bytes'  => strlen($html),
            ]);

            $tmpPath = tempnam(sys_get_temp_dir(), 'carteirinhas-lote-') . '.pdf';
            $t0 = microtime(true);

            try {
                BrowsershotHelper::configureChromePath(
                    Browsershot::html($html)
                        ->format('A4')
                        ->margins(8, 8, 8, 8)
                        ->showBackground()
                        ->emulateMedia('screen')
                )->timeout(120)->save($tmpPath);
            } catch (\Throwable $pdfEx) {
                @unlink($tmpPath);
                Log::error('[carteirinha] carteirinhasLotePdf — Browsershot falhou', [
                    'company_id' => $companyId,
                    'cards'      => count($cards),
                    'seconds'    => round(microtime(true) - $t0, 2),
                    'exception'  => $pdfEx::class,
                    'message'    => $pdfEx->getMessage(),
                ]);
                throw $pdfEx;
            }

            $pdf = @file_get_contents($tmpPath);
            @unlink($tmpPath);

            if ($pdf === false || $pdf === '') {
                abort(500, 'Falha ao ler o PDF gerado.');
            }

            Log::info('[carteirinha] carteirinhasLotePdf — PDF gerado', [
                'cards'     => count($cards),
                'pdf_bytes' => strlen($pdf),
                'seconds'   => round(microtime(true) - $t0, 2),
            ]);

            $filename = 'carteirinhas-lote-' . count($cards) . '.pdf';

            return response($pdf, 200, [
                'Content-Type'        => 'application/pdf',
                'Content-Disposition' => 'inline; filename="' . $filename . '"',
            ]);
        } catch (\Throwable $e) {
            Log::error('[carteirinha] carteirinhasLotePdf falhou', [
                'company_id' => $companyId,
                'ids'        => $ids,
                'exception'  => $e::class,
                'message'    => $e->getMessage(),
                'file'       => $e->getFile(),
                'line'       => $e->getLine(),
            ]);

            if ($e instanceof \Symfony\Component\HttpKernel\Exception\HttpException) {
                throw $e;
            }
            abort(500, 'Erro ao gerar o PDF em lote: ' . $e->getMessage());
        }
    }

    /**
     * Exclui um fiel via React (sessão web). Isola por company_id da sessão.
     */
    public function destroyReact(Request $request, $id)
    {
        try {
            $companyId = session('active_company_id');

            $fiel = Fiel::where('company_id', $companyId)->findOrFail($id);

            \DB::transaction(function () use ($fiel) {
                $fiel->addresses()->delete();
                $fiel->contacts()->delete();
                $fiel->tithe()->delete();
                $fiel->complementaryData()->delete();
                $fiel->delete();
            });

            return response()->json(['message' => 'Fiel excluído com sucesso.'], 200);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json(['message' => 'Fiel não encontrado.'], 404);
        } catch (\Throwable $e) {
            \Log::error('destroyReact Fiel: ' . $e->getMessage());
            return response()->json(['message' => 'Erro ao excluir o fiel.'], 500);
        }
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
