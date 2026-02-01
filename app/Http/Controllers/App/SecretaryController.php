<?php

namespace App\Http\Controllers\App;

use App\Http\Controllers\Controller;
use App\Models\Address;
use App\Models\Company;
use App\Models\FormationStage;
use App\Models\MemberFormationPeriod;
use App\Models\ReligiousMember;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;
use Yajra\DataTables\Facades\DataTables;

class SecretaryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $formationStages = FormationStage::where('is_active', true)
            ->orderBy('sort_order')
            ->get();

        $companies = Company::where('status', 'active')
            ->orderBy('name')
            ->get();

        return view('app.modules.secretary.index', compact('formationStages', 'companies'));
    }

    /**
     * Get statistics for tabs
     */
    public function getStats(Request $request)
    {
        $query = ReligiousMember::with(['province', 'role', 'currentStage'])
            ->where('is_active', true);

        // Contar por cada categoria
        $stats = [
            'todos' => (clone $query)->count(),
            'presbiteros' => (clone $query)->whereHas('role', function($q) {
                $q->where('slug', 'presbitero');
            })->count(),
            'diaconos' => (clone $query)->whereHas('role', function($q) {
                $q->where('slug', 'diacono');
            })->count(),
            'irmaos' => (clone $query)->whereHas('role', function($q) {
                $q->where('slug', 'irmao');
            })->count(),
            'votos_simples' => (clone $query)->whereNotNull('temporary_profession_date')
                                             ->whereNull('perpetual_profession_date')
                                             ->count()
        ];

        return response()->json($stats);
    }

    /**
     * Get data for DataTables
     */
    public function getData(Request $request)
    {
        $query = ReligiousMember::with([
                'province', 
                'role', 
                'currentStage',
                'formationPeriods' => function($q) {
                    $q->where('is_current', true)->with('company');
                }
            ])
            ->where('is_active', true);

        // Aplicar filtros baseados na tab ativa
        if ($request->has('filter') && $request->filter) {
            $filter = json_decode($request->filter, true);
            
            if (isset($filter['role_slug'])) {
                $query->whereHas('role', function($q) use ($filter) {
                    $q->where('slug', $filter['role_slug']);
                });
            }
            
            if (isset($filter['profession']) && $filter['profession'] === 'temporaria') {
                $query->whereNotNull('temporary_profession_date')
                      ->whereNull('perpetual_profession_date');
            }
        }

        return DataTables::of($query)
            ->addColumn('checkbox', function ($member) {
                return '<div class="form-check form-check-sm form-check-custom form-check-solid">
                    <input class="form-check-input" type="checkbox" value="'.$member->id.'" />
                </div>';
            })
            ->addColumn('nome', function ($member) {
                // Avatar
                $avatarHtml = '';
                if ($member->avatar) {
                    $avatarUrl = route('file', ['path' => $member->avatar]);
                    $avatarHtml = '<div class="symbol symbol-45px symbol-circle">
                        <img src="'.$avatarUrl.'" alt="'.$member->name.'" />
                    </div>';
                } else {
                    $avatarHtml = '<div class="symbol symbol-45px symbol-circle">
                        <div class="symbol-label fs-6 bg-light-primary text-primary fw-bold">
                            '.strtoupper(substr($member->name, 0, 1)).'
                        </div>
                    </div>';
                }
                
                // Badges da linha 1
                $badges = '';
                
                // Badge de função (Presbítero, Diácono, Irmão)
                if ($member->role) {
                    $roleVariants = [
                        'presbitero' => 'success',
                        'diacono' => 'warning', 
                        'irmao' => 'primary'
                    ];
                    $variant = $roleVariants[$member->role->slug] ?? 'secondary';
                    $badges .= '<span class="badge badge-light-'.$variant.' badge-sm ms-2">'.$member->role->name.'</span>';
                }
                
                // Badge de etapa atual
                if ($member->currentStage) {
                    $badges .= '<span class="badge badge-light-info badge-sm ms-1">'.$member->currentStage->name.'</span>';
                }
                
                // Badge de status ativo
                if ($member->is_active) {
                    $badges .= '<span class="badge badge-light-success badge-sm ms-1">Ativo</span>';
                } else {
                    $badges .= '<span class="badge badge-light-danger badge-sm ms-1">Inativo</span>';
                }
                
                // Linha secundária
                $secondaryInfo = [];
                
                // Província
                if ($member->province) {
                    $secondaryInfo[] = $member->province->name;
                }
                
                // Casa/local atual do período de formação (já carregado via eager loading)
                $currentPeriod = $member->formationPeriods->first();
                if ($currentPeriod && $currentPeriod->company) {
                    $secondaryInfo[] = $currentPeriod->company->name;
                }
                
                $secondaryLine = !empty($secondaryInfo) 
                    ? '<div class="fw-semibold text-muted fs-7">'.implode(' • ', $secondaryInfo).'</div>' 
                    : '';
                
                return '<div class="d-flex align-items-center">
                    '.$avatarHtml.'
                    <div class="ms-3">
                        <div class="d-flex align-items-center flex-wrap">
                            <a href="#" class="fs-6 fw-bold text-gray-900 text-hover-primary" data-action="view" data-id="'.$member->id.'">'.$member->name.'</a>
                            '.$badges.'
                        </div>
                        '.$secondaryLine.'
                    </div>
                </div>';
            })
            ->addColumn('provincia', function ($member) {
                return $member->province ? $member->province->name : '-';
            })
            ->addColumn('funcao', function ($member) {
                if (!$member->role) return '-';
                
                $variants = [
                    'presbitero' => 'success',
                    'diacono' => 'warning', 
                    'irmao' => 'primary'
                ];
                
                $variant = $variants[$member->role->slug] ?? 'secondary';
                
                return '<span class="badge badge-light-'.$variant.'">'.$member->role->name.'</span>';
            })
            ->addColumn('etapa_atual', function ($member) {
                if (!$member->currentStage) return '-';
                
                return '<span class="badge badge-light-info">'.$member->currentStage->name.'</span>';
            })
            ->addColumn('data_chave', function ($member) {
                // Definir data-chave baseada na função
                if ($member->role) {
                    switch ($member->role->slug) {
                        case 'presbitero':
                            return $member->priestly_ordination_date ? 
                                $member->priestly_ordination_date->format('d/m/Y') : '-';
                        case 'diacono':
                            return $member->diaconal_ordination_date ? 
                                $member->diaconal_ordination_date->format('d/m/Y') : '-';
                        case 'irmao':
                            // Priorizar perpétua, senão temporária
                            if ($member->perpetual_profession_date) {
                                return $member->perpetual_profession_date->format('d/m/Y');
                            } elseif ($member->temporary_profession_date) {
                                return $member->temporary_profession_date->format('d/m/Y');
                            }
                            return '-';
                    }
                }
                return '-';
            })
            ->addColumn('acoes', function ($member) {
                return '<div class="dropdown text-end">
                    <button class="btn btn-sm btn-light btn-active-light-primary" type="button" data-bs-toggle="dropdown">
                        Ações
                    </button>
                    <ul class="dropdown-menu">
                        <li>
                        <a class="dropdown-item" href="#" data-action="view" data-id="'.$member->id.'">
                            <i class="fa-regular fa-eye me-2"></i>Ver
                        </a>
                        </li>
                        <li>
                        <a class="dropdown-item" href="#" data-action="edit" data-id="'.$member->id.'">
                            <i class="fa-regular fa-pen-to-square me-2"></i>Editar
                        </a>
                        </li>
                        <li>
                        <a class="dropdown-item text-danger" href="#" data-action="delete" data-id="'.$member->id.'">
                            <i class="fa-regular fa-trash-can me-2 text-danger"></i>Excluir
                        </a>
                        </li>
                    </ul>
                </div>';
            })
            ->rawColumns(['checkbox', 'nome', 'funcao', 'etapa_atual', 'acoes'])
            ->make(true);
    }

    /**
     * Store a newly created member in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nome' => 'required|string|max:255',
            'current_stage_id' => 'required|exists:formation_stages,id',
            'data_nascimento' => 'required|date_format:d/m/Y',
            'funcao' => 'nullable|string|max:50',
            'provincia' => 'nullable|string|max:50',
            'cpf' => 'nullable|string|max:20',
            // Campos de endereço (irão para tabela adresses)
            'cep' => 'nullable|string|max:10',
            'bairro' => 'nullable|string|max:255',
            'logradouro' => 'nullable|string|max:255',
            'numero' => 'nullable|string|max:20',
            'localidade' => 'nullable|string|max:255',
            'uf' => 'nullable|string|max:2',
            'observacoes' => 'nullable|string',
            'disponivel_todas_casas' => 'nullable|boolean',
            'avatar' => 'nullable|image|mimes:jpg,jpeg,png|max:5120', // 5MB max
            'stages_json' => 'nullable|json',
        ]);

        try {
            DB::beginTransaction();

            // Converter data do formato brasileiro para o formato do banco
            $dataNascimento = Carbon::createFromFormat('d/m/Y', $validated['data_nascimento']);

            // Criar o membro religioso
            $member = ReligiousMember::create([
                'name' => $validated['nome'],
                'current_stage_id' => $validated['current_stage_id'],
                'birth_date' => $dataNascimento,
                'order_registration_number' => $validated['funcao'] ?? null,
                'cpf' => $validated['cpf'] ?? null,
                'observacoes' => $validated['observacoes'] ?? null,
                'disponivel_todas_casas' => $request->boolean('disponivel_todas_casas', true),
                'is_active' => true,
            ]);

            // Processar upload do avatar
            if ($request->hasFile('avatar')) {
                $avatarPath = $this->processAvatar($request->file('avatar'), $member->id);
                $member->update(['avatar' => $avatarPath]);
            }

            // Salvar endereço de origem se houver dados
            $this->saveOriginAddress($member, $validated);

            // Processar períodos de formação
            if (!empty($validated['stages_json'])) {
                $this->saveFormationPeriods($member, $validated['stages_json']);
            }

            DB::commit();

            // Recarregar o membro com relacionamentos
            $member->load(['currentStage', 'formationPeriods', 'addresses']);

            return response()->json([
                'success' => true,
                'message' => 'Membro cadastrado com sucesso!',
                'member' => $member,
                'avatar_url' => $member->avatar_url
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Erro ao cadastrar membro religioso: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Erro ao cadastrar o membro. Tente novamente.',
                'error' => config('app.debug') ? $e->getMessage() : null
            ], 500);
        }
    }

    /**
     * Salva o endereço de origem do membro
     */
    private function saveOriginAddress(ReligiousMember $member, array $validated): void
    {
        // Verificar se tem algum dado de endereço preenchido
        $hasAddressData = !empty($validated['cep']) ||
                          !empty($validated['logradouro']) ||
                          !empty($validated['bairro']) ||
                          !empty($validated['localidade']);

        if (!$hasAddressData) {
            return;
        }

        // Criar o endereço na tabela adresses
        $address = Address::create([
            'cep' => $validated['cep'] ?? null,
            'rua' => $validated['logradouro'] ?? null,
            'numero' => $validated['numero'] ?? null,
            'bairro' => $validated['bairro'] ?? null,
            'cidade' => $validated['localidade'] ?? null,
            'uf' => $validated['uf'] ?? null,
        ]);

        // Relacionar com o membro (tipo: origem)
        $member->addresses()->attach($address->id, ['tipo' => 'origem']);
    }

    /**
     * Processa e salva o avatar do membro
     */
    private function processAvatar($file, int $memberId): string
    {
        // Criar o diretório se não existir
        $directory = "avatars/members/{$memberId}";
        
        // Gerar nome único para o arquivo
        $filename = 'avatar_' . time() . '.jpg';
        $fullPath = "{$directory}/{$filename}";

        // Criar instância do ImageManager com driver GD
        $manager = new ImageManager(new Driver());
        
        // Redimensionar imagem para 300x300 com crop centralizado
        $image = $manager->read($file);
        $image->cover(300, 300);

        // Salvar no disco public
        Storage::disk('public')->put($fullPath, $image->toJpeg(85));

        return $fullPath;
    }

    /**
     * Salva os períodos de formação do membro
     */
    private function saveFormationPeriods(ReligiousMember $member, string $stagesJson): void
    {
        $stages = json_decode($stagesJson, true);

        if (!is_array($stages)) {
            return;
        }

        foreach ($stages as $stageData) {
            // Converter datas do formato brasileiro
            $startDate = null;
            $endDate = null;

            if (!empty($stageData['start_date'])) {
                $startDate = Carbon::createFromFormat('d/m/Y', $stageData['start_date'])->format('Y-m-d');
            }

            if (!empty($stageData['end_date'])) {
                $endDate = Carbon::createFromFormat('d/m/Y', $stageData['end_date'])->format('Y-m-d');
            }

            // Só criar registro se tiver data inicial
            if ($startDate) {
                MemberFormationPeriod::create([
                    'religious_member_id' => $member->id,
                    'formation_stage_id' => $stageData['stage_id'],
                    'company_id' => !empty($stageData['company_id']) ? $stageData['company_id'] : null,
                    'start_date' => $startDate,
                    'end_date' => $endDate,
                    'is_current' => $stageData['is_current'] ?? false,
                ]);
            }
        }
    }

    /**
     * Display the specified member.
     */
    public function show(ReligiousMember $member)
    {
        $member->load([
            'province', 
            'role', 
            'currentStage', 
            'addresses',
            'formationPeriods' => function($q) {
                $q->with(['formationStage', 'company'])->orderBy('start_date');
            }
        ]);
        
        // Buscar endereço de origem
        $originAddress = $member->addresses->first(function ($address) {
            return ($address->pivot->tipo ?? null) === 'origem';
        }) ?? $member->addresses->first();
        
        return view('app.modules.secretary.show', compact('member', 'originAddress'));
    }

    /**
     * Show the form for editing the specified member.
     */
    public function edit(ReligiousMember $member)
    {
        $member->load(['province', 'role', 'currentStage', 'addresses', 'formationPeriods']);
        
        // Buscar endereço de origem
        $originAddress = $member->addresses->first(function ($address) {
            return ($address->pivot->tipo ?? null) === 'origem';
        }) ?? $member->addresses->first();
        
        // Mapear para o formato do formulário
        $memberData = [
            'id' => $member->id,
            'nome' => $member->name,
            'nome_religioso' => $member->religious_name,
            'email' => $member->email,
            'telefone' => $member->phone,
            'data_nascimento' => $member->birth_date?->format('Y-m-d'),
            'naturalidade' => $member->birthplace,
            'role_slug' => $member->role?->slug,
            'provincia_id' => $member->province_id,
            'current_stage_id' => $member->current_stage_id,
            'funcao' => $member->order_registration_number,
            'cpf' => $member->cpf,
            'profession' => $member->perpetual_profession_date ? 'perpetua' : ($member->temporary_profession_date ? 'temporaria' : null),
            'data_profissao' => $member->perpetual_profession_date?->format('Y-m-d') ?? $member->temporary_profession_date?->format('Y-m-d'),
            'data_ordenacao' => $member->priestly_ordination_date?->format('Y-m-d') ?? $member->diaconal_ordination_date?->format('Y-m-d'),
            'local_ordenacao' => $member->ordination_place,
            'observacoes' => $member->notes,
            'disponivel_todas_casas' => $member->disponivel_todas_casas ?? true,
            'avatar_url' => $member->avatar_url,
            // Endereço de origem
            'endereco' => $originAddress ? [
                'cep' => $originAddress->cep,
                'bairro' => $originAddress->bairro,
                'rua' => $originAddress->rua,
                'numero' => $originAddress->numero,
                'cidade' => $originAddress->cidade,
                'uf' => $originAddress->uf,
            ] : null,
            // Períodos de formação
            'formation_periods' => $member->formationPeriods->map(function ($period) {
                return [
                    'id' => $period->id,
                    'formation_stage_id' => $period->formation_stage_id,
                    'start_date' => $period->start_date?->format('Y-m-d'),
                    'end_date' => $period->end_date?->format('Y-m-d'),
                    'company_id' => $period->company_id,
                    'is_current' => $period->is_current,
                ];
            })->toArray(),
        ];
        
        return response()->json([
            'success' => true,
            'member' => $memberData
        ]);
    }

    /**
     * Update the specified member in storage.
     */
    public function update(Request $request, ReligiousMember $member)
    {
        $validated = $request->validate([
            'nome' => 'required|string|max:255',
            'current_stage_id' => 'required|exists:formation_stages,id',
            'data_nascimento' => 'required|date_format:d/m/Y',
            'funcao' => 'nullable|string|max:50',
            'provincia' => 'nullable|string|max:50',
            'cpf' => 'nullable|string|max:20',
            // Campos de endereço
            'cep' => 'nullable|string|max:10',
            'bairro' => 'nullable|string|max:255',
            'logradouro' => 'nullable|string|max:255',
            'numero' => 'nullable|string|max:20',
            'localidade' => 'nullable|string|max:255',
            'uf' => 'nullable|string|max:2',
            'observacoes' => 'nullable|string',
            'disponivel_todas_casas' => 'nullable|boolean',
            'avatar' => 'nullable|image|mimes:jpg,jpeg,png|max:5120',
            'avatar_remove' => 'nullable|string',
            'stages_json' => 'nullable|json',
        ]);

        try {
            DB::beginTransaction();

            // Converter data do formato brasileiro para o formato do banco
            $dataNascimento = Carbon::createFromFormat('d/m/Y', $validated['data_nascimento']);

            // Atualizar dados básicos do membro
            $member->update([
                'name' => $validated['nome'],
                'current_stage_id' => $validated['current_stage_id'],
                'birth_date' => $dataNascimento,
                'order_registration_number' => $validated['funcao'] ?? null,
                'cpf' => $validated['cpf'] ?? null,
                'observacoes' => $validated['observacoes'] ?? null,
                'disponivel_todas_casas' => $request->boolean('disponivel_todas_casas', true),
            ]);

            // Processar avatar
            if ($request->hasFile('avatar')) {
                // Deletar avatar antigo se existir
                if ($member->avatar) {
                    Storage::disk('public')->delete($member->avatar);
                }
                $avatarPath = $this->processAvatar($request->file('avatar'), $member->id);
                $member->update(['avatar' => $avatarPath]);
            } elseif ($request->input('avatar_remove') === '1') {
                // Remover avatar se solicitado
                if ($member->avatar) {
                    Storage::disk('public')->delete($member->avatar);
                    $member->update(['avatar' => null]);
                }
            }

            // Atualizar endereço de origem
            $this->updateOriginAddress($member, $validated);

            // Processar períodos de formação
            if (!empty($validated['stages_json'])) {
                $this->updateFormationPeriods($member, $validated['stages_json']);
            }

            DB::commit();

            // Recarregar o membro com relacionamentos
            $member->load(['currentStage', 'formationPeriods', 'addresses']);

            return response()->json([
                'success' => true,
                'message' => 'Membro atualizado com sucesso!',
                'member' => $member,
                'avatar_url' => $member->avatar_url
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Erro ao atualizar membro religioso: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Erro ao atualizar o membro. Tente novamente.',
                'error' => config('app.debug') ? $e->getMessage() : null
            ], 500);
        }
    }

    /**
     * Atualiza o endereço de origem do membro
     */
    private function updateOriginAddress(ReligiousMember $member, array $validated): void
    {
        $hasAddressData = !empty($validated['cep']) ||
                          !empty($validated['logradouro']) ||
                          !empty($validated['bairro']) ||
                          !empty($validated['localidade']);

        // Buscar endereço de origem existente
        $existingAddress = $member->addresses()->wherePivot('tipo', 'origem')->first();

        if (!$hasAddressData) {
            // Se não tem dados e existe endereço, remove
            if ($existingAddress) {
                $member->addresses()->detach($existingAddress->id);
                $existingAddress->delete();
            }
            return;
        }

        if ($existingAddress) {
            // Atualizar endereço existente
            $existingAddress->update([
                'cep' => $validated['cep'] ?? null,
                'rua' => $validated['logradouro'] ?? null,
                'numero' => $validated['numero'] ?? null,
                'bairro' => $validated['bairro'] ?? null,
                'cidade' => $validated['localidade'] ?? null,
                'uf' => $validated['uf'] ?? null,
            ]);
        } else {
            // Criar novo endereço
            $address = Address::create([
                'cep' => $validated['cep'] ?? null,
                'rua' => $validated['logradouro'] ?? null,
                'numero' => $validated['numero'] ?? null,
                'bairro' => $validated['bairro'] ?? null,
                'cidade' => $validated['localidade'] ?? null,
                'uf' => $validated['uf'] ?? null,
            ]);
            $member->addresses()->attach($address->id, ['tipo' => 'origem']);
        }
    }

    /**
     * Atualiza os períodos de formação do membro
     */
    private function updateFormationPeriods(ReligiousMember $member, string $stagesJson): void
    {
        $stages = json_decode($stagesJson, true);

        if (!is_array($stages)) {
            return;
        }

        // Remover períodos antigos que não estão mais na lista
        $stageIds = collect($stages)->pluck('stage_id')->toArray();
        $member->formationPeriods()
            ->whereNotIn('formation_stage_id', $stageIds)
            ->delete();

        foreach ($stages as $stageData) {
            $startDate = null;
            $endDate = null;

            if (!empty($stageData['start_date'])) {
                $startDate = Carbon::createFromFormat('d/m/Y', $stageData['start_date'])->format('Y-m-d');
            }

            if (!empty($stageData['end_date'])) {
                $endDate = Carbon::createFromFormat('d/m/Y', $stageData['end_date'])->format('Y-m-d');
            }

            if ($startDate) {
                MemberFormationPeriod::updateOrCreate(
                    [
                        'religious_member_id' => $member->id,
                        'formation_stage_id' => $stageData['stage_id'],
                    ],
                    [
                        'company_id' => !empty($stageData['company_id']) ? $stageData['company_id'] : null,
                        'start_date' => $startDate,
                        'end_date' => $endDate,
                        'is_current' => $stageData['is_current'] ?? false,
                    ]
                );
            }
        }
    }

    /**
     * Remove the specified member from storage.
     */
    public function destroy(ReligiousMember $member)
    {
        // Soft delete - apenas marca como inativo
        $member->update(['is_active' => false]);

        return response()->json([
            'success' => true,
            'message' => 'Membro removido com sucesso!'
        ]);
    }
}
