<?php

namespace App\Http\Controllers\App\Cemiterio;

use App\Http\Controllers\Controller;
use App\Models\Cemiterio\Sepultado;
use App\Models\Cemiterio\SepultadoResponsavel;
use App\Models\Cemiterio\Sepultura;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class DifuntoController extends Controller
{
    public function index(Request $request)
    {
        $companyId = User::getCompany()->company_id;

        $page    = max(1, (int) $request->query('page', 1));
        $perPage = min(100, max(1, (int) $request->query('per_page', 20)));
        $search  = trim($request->query('search', ''));
        $sortBy  = $request->query('sort_by', 'data_falecimento');
        $sortDir = strtolower($request->query('sort_dir', 'desc'));

        $columnMap = [
            'nome_completo'    => 'nome',
            'data_falecimento' => 'data_falecimento',
            'cpf'              => 'cpf',
        ];
        $dbColumn = $columnMap[$sortBy] ?? 'data_falecimento';
        $sortDir  = in_array($sortDir, ['asc', 'desc']) ? $sortDir : 'desc';

        $query = Sepultado::where('company_id', $companyId)
            ->with('responsaveis');

        if ($search !== '') {
            $query->where(function ($q) use ($search) {
                $q->where('nome', 'like', "%{$search}%")
                  ->orWhere('cpf', 'like', "%{$search}%");
            });
        }

        $total = $query->count();

        $difuntos = $query
            ->orderBy($dbColumn, $sortDir)
            ->offset(($page - 1) * $perPage)
            ->limit($perPage)
            ->get();

        $data = $difuntos->map(fn($d) => [
            'id'               => $d->id,
            'nome_completo'    => $d->nome,
            'cpf'              => $d->cpf,
            'avatar'           => $d->avatar ? '/file/' . ltrim($d->avatar, '/') : null,
            'data_nascimento'  => $d->data_nascimento?->toDateString(),
            'data_falecimento' => $d->data_falecimento?->toDateString(),
            'nome_responsavel' => $d->responsaveis->first()?->nome,
            'tumulo_atual'     => $d->tumulo_codigo,
        ]);

        return response()->json([
            'success' => true,
            'data'    => $data,
            'total'   => $total,
        ]);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'nome_completo'              => 'required|string|max:255',
            'cpf'                        => 'nullable|string|max:20',
            'data_nascimento'            => 'nullable|date',
            'data_falecimento'           => 'required|date',
            'data_sepultamento'          => 'nullable|date',
            'causa_mortis'               => 'nullable|string|max:255',
            'tumulo_codigo'              => 'nullable|string|max:50',
            'sepultura_id'               => 'nullable|integer',
            'relacionamento'             => 'nullable|string|max:255',
            'informacoes_atestado_obito' => 'nullable|string',
            'livro_sepultamento'         => 'nullable|string|max:100',
            'folha_sepultamento'         => 'nullable|string|max:100',
            'numero_sepultamento'        => 'nullable|string|max:100',
            'observacoes'                => 'nullable|string',
            'avatar'                     => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
            'novas_imagens'              => 'nullable|array|max:10',
            'novas_imagens.*'            => 'image|mimes:jpg,jpeg,png,webp|max:5120',
            'responsaveis'               => 'nullable|string', // JSON string
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Dados inválidos.',
                'errors'  => $validator->errors(),
            ], 422);
        }

        $company = User::getCompany();
        $user    = Auth::user();

        $avatarPath = null;
        if ($request->hasFile('avatar')) {
            $avatarPath = $request->file('avatar')->store('cemiterio/avatars', 'public');
        }

        $imagensPaths = [];
        if ($request->hasFile('novas_imagens')) {
            foreach ($request->file('novas_imagens') as $img) {
                $imagensPaths[] = $img->store('cemiterio/imagens', 'public');
            }
        }

        $sepultado = Sepultado::create([
            'company_id'                 => $company->company_id,
            'nome'                       => $request->input('nome_completo'),
            'cpf'                        => $request->input('cpf'),
            'data_nascimento'            => $request->input('data_nascimento') ?: null,
            'data_falecimento'           => $request->input('data_falecimento'),
            'data_sepultamento'          => $request->input('data_sepultamento') ?: null,
            'causa_mortis'               => $request->input('causa_mortis'),
            'tumulo_codigo'              => $request->input('tumulo_codigo'),
            'sepultura_id'               => $request->input('sepultura_id') ?: null,
            'relacionamento'             => $request->input('relacionamento'),
            'informacoes_atestado_obito' => $request->input('informacoes_atestado_obito'),
            'livro_sepultamento'         => $request->input('livro_sepultamento'),
            'folha_sepultamento'         => $request->input('folha_sepultamento'),
            'numero_sepultamento'        => $request->input('numero_sepultamento'),
            'observacoes'                => $request->input('observacoes'),
            'avatar'                     => $avatarPath,
            'imagens'                    => $imagensPaths ?: null,
            'created_by'                 => $user->id,
            'created_by_name'            => $user->name,
        ]);

        // Salvar responsáveis
        if ($request->filled('responsaveis')) {
            $responsaveis = json_decode($request->input('responsaveis'), true);
            if (is_array($responsaveis)) {
                foreach ($responsaveis as $r) {
                    SepultadoResponsavel::create([
                        'sepultado_id' => $sepultado->id,
                        'nome'         => $r['nome'] ?? '',
                        'telefone'     => $r['telefone'] ?? null,
                        'cep'          => $r['cep'] ?? null,
                        'logradouro'   => $r['logradouro'] ?? null,
                        'numero'       => $r['numero'] ?? null,
                        'bairro'       => $r['bairro'] ?? null,
                        'cidade'       => $r['cidade'] ?? null,
                        'uf'           => $r['uf'] ?? null,
                    ]);
                }
            }
        }

        return response()->json([
            'success' => true,
            'message' => 'Difunto cadastrado com sucesso!',
            'data'    => $sepultado->load('responsaveis'),
        ], 201);
    }

    public function show($id)
    {
        $companyId = User::getCompany()->company_id;
        $d = Sepultado::where('company_id', $companyId)->with(['responsaveis', 'sepultura'])->findOrFail($id);

        return response()->json([
            'success' => true,
            'data'    => [
                'id'                         => $d->id,
                'nome_completo'              => $d->nome,
                'cpf'                        => $d->cpf ?? '',
                'avatar'                     => $d->avatar ? '/file/' . ltrim($d->avatar, '/') : null,
                'data_nascimento'            => $d->data_nascimento?->toDateString() ?? '',
                'data_falecimento'           => $d->data_falecimento?->toDateString() ?? '',
                'data_sepultamento'          => $d->data_sepultamento?->toDateString() ?? '',
                'causa_mortis'               => $d->causa_mortis ?? '',
                'tumulo_codigo'              => $d->tumulo_codigo ?? '',
                'sepultura_id'               => $d->sepultura_id,
                'sepultura_label'            => $d->sepultura?->codigo_sepultura ?? '',
                'relacionamento'             => $d->relacionamento ?? '',
                'informacoes_atestado_obito' => $d->informacoes_atestado_obito ?? '',
                'livro_sepultamento'         => $d->livro_sepultamento ?? '',
                'folha_sepultamento'         => $d->folha_sepultamento ?? '',
                'numero_sepultamento'        => $d->numero_sepultamento ?? '',
                'observacoes'                => $d->observacoes ?? '',
                'imagens'                    => collect($d->imagens ?? [])
                    ->map(fn($path) => ['id' => $path, 'url' => '/file/' . ltrim($path, '/')])
                    ->values()
                    ->all(),
                'responsaveis'               => $d->responsaveis->map(fn($r) => [
                    'id'         => (string) $r->id,
                    'nome'       => $r->nome,
                    'telefone'   => $r->telefone ?? '',
                    'cep'        => $r->cep ?? '',
                    'logradouro' => $r->logradouro ?? '',
                    'numero'     => $r->numero ?? '',
                    'bairro'     => $r->bairro ?? '',
                    'cidade'     => $r->cidade ?? '',
                    'uf'         => $r->uf ?? '',
                ]),
            ],
        ]);
    }

    public function update(Request $request, $id)
    {
        $companyId = User::getCompany()->company_id;
        $sepultado = Sepultado::where('company_id', $companyId)->findOrFail($id);

        $validator = Validator::make($request->all(), [
            'nome_completo'              => 'required|string|max:255',
            'cpf'                        => 'nullable|string|max:20',
            'data_nascimento'            => 'nullable|date',
            'data_falecimento'           => 'required|date',
            'data_sepultamento'          => 'nullable|date',
            'causa_mortis'               => 'nullable|string|max:255',
            'tumulo_codigo'              => 'nullable|string|max:50',
            'sepultura_id'               => 'nullable|integer',
            'relacionamento'             => 'nullable|string|max:255',
            'informacoes_atestado_obito' => 'nullable|string',
            'livro_sepultamento'         => 'nullable|string|max:100',
            'folha_sepultamento'         => 'nullable|string|max:100',
            'numero_sepultamento'        => 'nullable|string|max:100',
            'observacoes'                => 'nullable|string',
            'avatar'                     => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
            'keep_imagens'               => 'nullable|string',
            'novas_imagens'              => 'nullable|array|max:10',
            'novas_imagens.*'            => 'image|mimes:jpg,jpeg,png,webp|max:5120',
            'responsaveis'               => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Dados inválidos.',
                'errors'  => $validator->errors(),
            ], 422);
        }

        if ($request->hasFile('avatar')) {
            if ($sepultado->avatar) {
                Storage::disk('public')->delete($sepultado->avatar);
            }
            $sepultado->avatar = $request->file('avatar')->store('cemiterio/avatars', 'public');
        }

        // Gerenciar imagens: remover as que não foram mantidas, adicionar novas
        $keepPaths = $request->filled('keep_imagens')
            ? (json_decode($request->input('keep_imagens'), true) ?? [])
            : [];
        $existingPaths = $sepultado->imagens ?? [];
        foreach ($existingPaths as $path) {
            if (!in_array($path, $keepPaths)) {
                Storage::disk('public')->delete($path);
            }
        }
        $novasPaths = $keepPaths;
        if ($request->hasFile('novas_imagens')) {
            foreach ($request->file('novas_imagens') as $img) {
                $novasPaths[] = $img->store('cemiterio/imagens', 'public');
            }
        }
        $sepultado->imagens = $novasPaths ?: null;

        $sepultado->fill([
            'nome'                       => $request->input('nome_completo'),
            'cpf'                        => $request->input('cpf'),
            'data_nascimento'            => $request->input('data_nascimento') ?: null,
            'data_falecimento'           => $request->input('data_falecimento'),
            'data_sepultamento'          => $request->input('data_sepultamento') ?: null,
            'causa_mortis'               => $request->input('causa_mortis'),
            'tumulo_codigo'              => $request->input('tumulo_codigo'),
            'sepultura_id'               => $request->input('sepultura_id') ?: null,
            'relacionamento'             => $request->input('relacionamento'),
            'informacoes_atestado_obito' => $request->input('informacoes_atestado_obito'),
            'livro_sepultamento'         => $request->input('livro_sepultamento'),
            'folha_sepultamento'         => $request->input('folha_sepultamento'),
            'numero_sepultamento'        => $request->input('numero_sepultamento'),
            'observacoes'                => $request->input('observacoes'),
        ]);
        $sepultado->save();

        // Sync responsáveis
        $sepultado->responsaveis()->delete();
        if ($request->filled('responsaveis')) {
            $responsaveis = json_decode($request->input('responsaveis'), true);
            if (is_array($responsaveis)) {
                foreach ($responsaveis as $r) {
                    SepultadoResponsavel::create([
                        'sepultado_id' => $sepultado->id,
                        'nome'         => $r['nome'] ?? '',
                        'telefone'     => $r['telefone'] ?? null,
                        'cep'          => $r['cep'] ?? null,
                        'logradouro'   => $r['logradouro'] ?? null,
                        'numero'       => $r['numero'] ?? null,
                        'bairro'       => $r['bairro'] ?? null,
                        'cidade'       => $r['cidade'] ?? null,
                        'uf'           => $r['uf'] ?? null,
                    ]);
                }
            }
        }

        return response()->json([
            'success' => true,
            'message' => 'Difunto atualizado com sucesso!',
            'data'    => $sepultado->load('responsaveis'),
        ]);
    }

    public function searchParentes(Request $request)
    {
        $companyId = User::getCompany()->company_id;
        $q = trim($request->query('q', ''));

        $responsaveis = SepultadoResponsavel::whereHas('sepultado', fn($sq) => $sq->where('company_id', $companyId))
            ->with(['sepultado' => fn($sq) => $sq->with('sepultura:id,codigo_sepultura,localizacao')])
            ->when($q !== '', fn($query) => $query->where('nome', 'like', "%{$q}%"))
            ->orderBy('nome')
            ->limit(30)
            ->get(['id', 'sepultado_id', 'nome', 'telefone']);

        $data = $responsaveis->map(fn($r) => [
            'responsavel_id'   => $r->id,
            'responsavel_nome' => $r->nome,
            'responsavel_tel'  => $r->telefone,
            'difunto_id'       => $r->sepultado->id,
            'difunto_nome'     => $r->sepultado->nome,
            'cpf'              => $r->sepultado->cpf,
            'sepultura_id'     => $r->sepultado->sepultura_id,
            'sepultura_codigo' => $r->sepultado->sepultura?->codigo_sepultura ?? $r->sepultado->tumulo_codigo ?? '',
            'sepultura_local'  => $r->sepultado->sepultura?->localizacao ?? '',
        ]);

        return response()->json(['success' => true, 'data' => $data]);
    }

    public function searchDifuntos(Request $request)
    {
        $companyId = User::getCompany()->company_id;
        $q = trim($request->query('q', ''));

        $difuntos = Sepultado::where('company_id', $companyId)
            ->with('sepultura:id,codigo_sepultura,localizacao')
            ->whereNotNull('sepultura_id')
            ->when($q !== '', fn($query) => $query->where(function ($sub) use ($q) {
                $sub->where('nome', 'like', "%{$q}%")
                    ->orWhere('cpf', 'like', "%{$q}%");
            }))
            ->orderBy('nome')
            ->limit(30)
            ->get(['id', 'nome', 'cpf', 'sepultura_id', 'tumulo_codigo']);

        $data = $difuntos->map(fn($d) => [
            'difunto_id'       => $d->id,
            'nome'             => $d->nome,
            'cpf'              => $d->cpf,
            'sepultura_id'     => $d->sepultura_id,
            'sepultura_codigo' => $d->sepultura?->codigo_sepultura ?? $d->tumulo_codigo ?? '',
            'sepultura_local'  => $d->sepultura?->localizacao ?? '',
        ]);

        return response()->json(['success' => true, 'data' => $data]);
    }

    public function searchSepulturas(Request $request)
    {
        $companyId = User::getCompany()->company_id;
        $q = trim($request->query('q', ''));

        $sepulturas = Sepultura::where('company_id', $companyId)
            ->when($q !== '', fn($query) => $query->where(function ($sub) use ($q) {
                $sub->where('codigo_sepultura', 'like', "%{$q}%")
                    ->orWhere('localizacao', 'like', "%{$q}%")
                    ->orWhere('tipo', 'like', "%{$q}%");
            }))
            ->orderBy('codigo_sepultura')
            ->limit(30)
            ->get(['id', 'codigo_sepultura', 'localizacao', 'tipo', 'status']);

        return response()->json(['success' => true, 'data' => $sepulturas]);
    }

    public function storeQuickSepultura(Request $request)
    {
        $company = User::getCompany();
        $user    = Auth::user();

        $validator = Validator::make($request->all(), [
            'codigo_sepultura' => 'required|string|max:255',
            'localizacao'      => 'nullable|string|max:255',
            'tipo'             => 'nullable|string|max:255',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Dados inválidos.',
                'errors'  => $validator->errors(),
            ], 422);
        }

        $sepultura = Sepultura::create([
            'company_id'       => $company->company_id,
            'codigo_sepultura' => $request->input('codigo_sepultura'),
            'localizacao'      => $request->input('localizacao'),
            'tipo'             => $request->input('tipo'),
            'status'           => 'Disponível',
            'created_by'       => $user->id,
            'created_by_name'  => $user->name,
        ]);

        return response()->json([
            'success' => true,
            'data'    => [
                'id'               => $sepultura->id,
                'codigo_sepultura' => $sepultura->codigo_sepultura,
                'localizacao'      => $sepultura->localizacao,
                'tipo'             => $sepultura->tipo,
                'status'           => $sepultura->status,
            ],
        ], 201);
    }
}
