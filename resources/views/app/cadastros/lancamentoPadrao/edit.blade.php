<x-tenant-app-layout>
    <!--begin::Main-->
    <div class="app-main flex-column flex-row-fluid" id="kt_app_main">
        <!--begin::Content wrapper-->
        <div class="d-flex flex-column flex-column-fluid">
            <!--begin::Toolbar-->
            <div id="kt_app_toolbar" class="app-toolbar py-3 py-lg-6">
                <!--begin::Toolbar container-->
                <div id="kt_app_toolbar_container" class="app-container container-xxl d-flex flex-stack">
                    <!--begin::Page title-->
                    <div class="page-title d-flex flex-column justify-content-center flex-wrap me-3">
                        <!--begin::Title-->
                        <h1 class="page-heading d-flex text-dark fw-bold fs-3 flex-column justify-content-center my-0">
                            Edição de Lançamento Padrão</h1>
                        <!--end::Title-->
                        <!--begin::Breadcrumb-->
                        <ul class="breadcrumb breadcrumb-separatorless fw-semibold fs-7 my-0 pt-1">
                            <!--begin::Item-->
                            <li class="breadcrumb-item text-muted">
                                <a href="{{ route('dashboard') }}" class="text-muted text-hover-primary">Dashboard</a>
                            </li>
                            <!--end::Item-->
                            <!--begin::Item-->
                            <li class="breadcrumb-item">
                                <span class="bullet bg-gray-400 w-5px h-2px"></span>
                            </li>
                            <!--end::Item-->
                            <!--begin::Item-->
                            <li class="breadcrumb-item text-muted">
                                <a href="{{ route('lancamentoPadrao.index') }}"
                                    class="text-muted text-hover-primary">Lista de Lançamento</a>
                            </li>
                            <!--end::Item--> <!--end::Item-->
                            <!--begin::Item-->
                            <li class="breadcrumb-item">
                                <span class="bullet bg-gray-400 w-5px h-2px"></span>
                            </li>
                            <!--end::Item-->
                            <!--begin::Item-->
                            <li class="breadcrumb-item text-muted">Criação</li>
                            <!--end::Item-->
                        </ul>
                        <!--end::Breadcrumb-->
                    </div>
                    <!--end::Page title-->
                    <!--begin::Actions-->

                    <!--end::Actions-->
                </div>
                <!--end::Toolbar container-->
            </div>
            <!--end::Toolbar-->
            <!--begin::Modal - Confirmar Exclusão-->

            <div class="modal fade" id="kt_modal_delete_card" tabindex="-1" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content">
                        <!-- Cabeçalho -->
                        <div class="modal-header">
                            <h5 class="modal-title text-danger fw-bold">Confirmar Exclusão</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"
                                aria-label="Close"></button>
                        </div>

                        <!-- Corpo -->
                        <div class="modal-body text-center">
                            <i class="bi bi-exclamation-circle-fill text-danger fs-2 mb-4"></i>
                            <p class="mb-0 fs-5 fw-semibold text-center">
                                Tem certeza que deseja excluir o registro <strong>#{{ $lp->description }}</strong>?
                            </p>
                            <small class="text-muted d-block mt-3">
                                Esta ação não pode ser desfeita.
                            </small>
                        </div>

                        <!-- Rodapé -->
                        <div class="modal-footer justify-content-center">
                            <form id="delete-form" method="POST"
                                action="{{ route('lancamentoPadrao.destroy', $lp->id) }}">
                                @csrf
                                @method('DELETE')
                                <button type="button" class="btn btn-secondary px-4"
                                    data-bs-dismiss="modal">Cancelar</button>
                                <button type="submit" class="btn btn-danger px-4">
                                    <i class="fas fa-trash-alt me-2"></i> Confirmar Exclusão
                                </button>
                            </form>
                        </div>
                    </div>
                </div>

            </div>
            <!--end::Modal - Confirmar Exclusão-->
            <!--begin::Content-->
            <div id="kt_app_content" class="app-content flex-column-fluid">
                <!--begin::Content container-->
                <div id="kt_app_content_container" class="app-container container-xxl">
                    <!--begin::Careers main-->
                    <div class="d-flex flex-column flex-xl-row">
                        <!--begin::Content-->
                        <div class="card bg-body me-xl-9 mb-9 mb-xl-0">
                            <div class="card-body">
                                <!--begin::Form-->
                                <!--begin::Form-->
                                <form class="form" method="POST"
                                    action="{{ route('lancamentoPadrao.update', $lp->id) }}"
                                    id="kt_modal_add_customer_form">
                                    @csrf
                                    @method('PUT') <!-- Use PUT ou PATCH para atualização -->

                                    <!-- Input oculto para data -->
                                    <input type="hidden" name="date" value="{{ now()->format('Y-m-d') }}" />

                                    <!--begin::Description-->
                                    <div class="mb-7">
                                        <!--begin::Title-->
                                        <h3 class="fs-1 text-gray-800 w-bolder mb-6">Editar - {{ $lp->description }}
                                        </h3>
                                        <!--end::Title-->
                                        <!--begin::Text-->
                                        <p class=" fs-4 text-gray-600 mb-2">
                                            O <strong>Lançamento Padrão</strong> é um conjunto pré-definido de
                                            informações
                                            contábeis ou financeiras que se repetem de forma recorrente, como pagamentos
                                            mensais, recebimentos frequentes ou despesas fixas.
                                        </p>
                                        <!--end::Text-->
                                    </div>
                                    <!--end::Description-->

                                    <!--begin::Input group-->
                                    <div class="row mb-5">
                                        <!--begin::Col - Nome do Lançamento-->
                                        <div class="col-md-8 fv-row">
                                            <label class="required fs-5 fw-semibold mb-2">Nome do Lançamento</label>
                                            <input type="text"
                                                class="form-control form-control-solid @error('description') is-invalid @enderror"
                                                placeholder="Digite o nome" name="description"
                                                value="{{ $lp->description }}" />
                                            @error('description')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                        <!--end::Col-->

                                        <!--begin::Col - Tipo-->
                                        <div class="col-md-4 fv-row">
                                            <label class="required fs-5 fw-semibold mb-2">Tipo</label>
                                            <select class="form-select form-select-solid" name="type"
                                                data-control="select2" data-placeholder="Selecione o tipo">
                                                <option></option>
                                                <option value="entrada" {{ $lp->type === 'entrada' ? 'selected' : '' }}>
                                                    Entrada</option>
                                                <option value="saida" {{ $lp->type === 'saida' ? 'selected' : '' }}>
                                                    Saída</option>
                                                <option value="ambos" {{ $lp->type === 'ambos' ? 'selected' : '' }}>
                                                    Ambos (Entrada e Saída)</option>
                                            </select>
                                            <div class="form-text">Use "Ambos" para lançamentos que servem para entrada e saída (ex: Transferências)</div>
                                            @error('type')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                        <!--end::Col-->
                                    </div>
                                    <!--end::Input group-->

                                    <!--begin::Input group-->
                                    <div class="row mb-5">
                                        <!--begin::Col - Categoria-->
                                        <div class="col-md-12 fv-row">
                                            <label class="required fs-5 fw-semibold mb-2">Categoria</label>
                                            <select
                                                class="form-select form-select-solid @error('category') is-invalid @enderror"
                                                name="category" data-control="select2"
                                                data-placeholder="Selecione uma categoria">
                                                <option value="">Selecione uma categoria...</option>
                                                <!-- Adicione as opções de categorias aqui -->
                                                <option value="Administrativo"
                                                    {{ $lp->category === 'Administrativo' ? 'selected' : '' }}>🏢
                                                    Administrativo</option>
                                                <option value="Administrativo"
                                                    {{ $lp->category === 'Administrativo' ? 'selected' : '' }}>
                                                    Administrativo</option>
                                                <option value="Alimentação"
                                                    {{ $lp->category === 'Alimentação' ? 'selected' : '' }}>🍴
                                                    Alimentação</option>
                                                <option value="Cerimônias"
                                                    {{ $lp->category === 'Cerimônias' ? 'selected' : '' }}>🎉
                                                    Cerimônias</option>
                                                <option value="Comércio"
                                                    {{ $lp->category === 'Comércio' ? 'selected' : '' }}>🛒 Comércio
                                                </option>
                                                <option value="Coletas"
                                                    {{ $lp->category === 'Coletas' ? 'selected' : '' }}>🗑️ Coletas
                                                </option>
                                                <option value="Comunicação"
                                                    {{ $lp->category === 'Comunicação' ? 'selected' : '' }}>📞
                                                    Comunicação</option>
                                                <option value="Contribuições"
                                                    {{ $lp->category === 'Contribuições' ? 'selected' : '' }}>💰
                                                    Contribuições</option>
                                                <option value="Doações"
                                                    {{ $lp->category === 'Doações' ? 'selected' : '' }}>🎁 Doações
                                                </option>
                                                <option value="Educação"
                                                    {{ $lp->category === 'Educação' ? 'selected' : '' }}>📚 Educação
                                                </option>
                                                <option value="Equipamentos"
                                                    {{ $lp->category === 'Equipamentos' ? 'selected' : '' }}>🛠️
                                                    Equipamentos</option>
                                                <option value="Eventos"
                                                    {{ $lp->category === 'Eventos' ? 'selected' : '' }}>🎪 Eventos
                                                </option>
                                                <option value="Intenções"
                                                    {{ $lp->category === 'Intenções' ? 'selected' : '' }}>🙏
                                                    Intenções</option>
                                                <option value="Liturgia"
                                                    {{ $lp->category === 'Liturgia' ? 'selected' : '' }}>⛪ Liturgia
                                                </option>
                                                <option value="Manutenção"
                                                    {{ $lp->category === 'Manutenção' ? 'selected' : '' }}>🔧
                                                    Manutenção</option>
                                                <option value="Material de escritório"
                                                    {{ $lp->category === 'Material de escritório' ? 'selected' : '' }}>
                                                    📎 Material de escritório</option>
                                                <option value="Pessoal"
                                                    {{ $lp->category === 'Pessoal' ? 'selected' : '' }}>👤 Pessoal
                                                </option>
                                                <option value="Rendimentos"
                                                    {{ $lp->category === 'Rendimentos' ? 'selected' : '' }}>💹
                                                    Rendimentos</option>
                                                <option value="Saúde"
                                                    {{ $lp->category === 'Saúde' ? 'selected' : '' }}>🏥 Saúde
                                                </option>
                                                <option value="Serviços essenciais"
                                                    {{ $lp->category === 'Serviços essenciais' ? 'selected' : '' }}>
                                                    ⚙️ Serviços essenciais</option>
                                                <option value="Suprimentos"
                                                    {{ $lp->category === 'Suprimentos' ? 'selected' : '' }}>📦
                                                    Suprimentos</option>
                                                <option value="Financeiro"
                                                    {{ $lp->category === 'Financeiro' ? 'selected' : '' }}>💳
                                                    Financeiro</option>
                                                <option value="Transporte"
                                                    {{ $lp->category === 'Transporte' ? 'selected' : '' }}>🚗
                                                    Transporte</option>
                                            </select>
                                            @error('category')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                        <!--end::Col-->
                                    </div>
                                    <!--end::Input group-->

                                    <!--begin::Separator-->
                                    <div class="separator mb-8"></div>
                                    <!--end::Separator-->
                                    <!-- Botão Sair (Voltar) -->
                                    <a href="{{ url()->previous() }}" class="btn btn-light me-3">
                                        <i class="bi bi-box-arrow-left me-2"></i> Sair
                                    </a>

                                    <!-- Botão Salvar (Ação Principal) -->
                                    <button type="submit" id="kt_modal_add_customer_submit"
                                        class="btn btn-primary me-3">
                                        <span class="indicator-label">
                                            <i class="bi bi-save me-2"></i> Salvar
                                        </span>
                                    </button>

                                    <!-- Botão Excluir (Ação Perigosa) -->
                                    <button type="button" class="btn btn-light-danger" data-bs-toggle="modal"
                                        data-bs-target="#kt_modal_delete_card">
                                        <i class="bi bi-trash3 me-2"></i> Excluir
                                    </button>
                                </form>
                                <!--end::Form-->
                            </div>
                        </div>
                        <!--end::Content-->
                        <!--begin::Sidebar-->
                        <div class="flex-column flex-lg-row-auto w-100 w-xl-450px">
                            <!--begin::Col-->
                            <div class="card card-flush bg-body mb-9">
                                <!--begin::List widget 8-->
                                <div class="card card-flush h-lg-100">
                                    <!--begin::Header-->
                                    <div class="card-header pt-7 mb-7">
                                        <!--begin::Title-->
                                        <h3 class="card-title align-items-start flex-column">
                                            <span class="card-label fw-bold text-gray-800">Últimos Cadastrados</span>
                                            <!-- Subtítulo/descrição: Exibindo mais detalhes -->
                                            <span class="text-gray-400 mt-1 fw-semibold fs-6">
                                                Total de {{ $lps->count() }} registros
                                            </span>
                                        </h3>
                                        <!--end::Title-->
                                        <!--begin::Toolbar-->
                                        <div class="card-toolbar">
                                            <a href="{{ route('lancamentoPadrao.index') }}"
                                                class="btn btn-sm btn-light">Ver todos</a>
                                        </div>
                                        <!--end::Toolbar-->
                                    </div>
                                    <!--end::Header-->
                                    <!--begin::Body-->
                                    <div class="card-body pt-0">
                                        <!--begin::Items-->
                                        <div class="m-0">
                                            @foreach ($lps->sortByDesc('created_at')->take(5) as $lp)
                                                <!--begin::Item-->
                                                <div class="d-flex flex-stack">
                                                    <!--begin::Emoji da Categoria-->
                                                    <span class="me-4 w-25px"
                                                        style="font-size: 25px; border-radius: 4px;">
                                                        {{ $lp->getCategoryEmoji() }}
                                                    </span>
                                                    <!--end::Emoji da Categoria-->
                                                    <!--begin::Section-->
                                                    <div class="d-flex flex-stack flex-row-fluid d-grid gap-2">
                                                        <!--begin::Content-->
                                                        <div class="me-5">
                                                            <!--begin::Title-->
                                                            <a href="{{ route('lancamentoPadrao.edit', $lp->id) }}"
                                                                class="text-gray-800 fw-bold text-hover-primary fs-6">{{ $lp->description }}</a>
                                                            <!--end::Title-->
                                                            <!--begin::Desc-->
                                                            <span
                                                                class="text-gray-400 fw-semibold fs-7 d-block text-start ps-0">{{ $lp->category }}</span>
                                                            <!--end::Desc-->
                                                        </div>
                                                        <!--end::Content-->
                                                        <!--begin::Info-->
                                                        <div class="d-flex align-items-center">

                                                            <!--begin::Label-->
                                                            <div class="m-0">
                                                                <!--begin::Label-->
                                                                @php
                                                                    // Define a cor (classe) e o ícone com base no tipo
                                                                    $isEntrada = $lp->type === 'entrada';
                                                                    $isAmbos = $lp->type === 'ambos';
                                                                    $colorClass = $isEntrada ? 'success' : ($isAmbos ? 'primary' : 'danger');
                                                                @endphp

                                                                <span
                                                                    class="badge badge-light-{{ $colorClass }} fs-base">
                                                                    <span
                                                                        class="svg-icon svg-icon-5 svg-icon-{{ $colorClass }} ms-n1">
                                                                        @if ($isAmbos)
                                                                            <!-- Ícone de SETAS DUPLAS (azul) para Ambos -->
                                                                            <svg width="24" height="24"
                                                                                viewBox="0 0 24 24" fill="none"
                                                                                xmlns="http://www.w3.org/2000/svg">
                                                                                <path d="M12 4L8 8H11V16H13V8H16L12 4Z" fill="currentColor" />
                                                                                <path d="M12 20L16 16H13V8H11V16H8L12 20Z" fill="currentColor" />
                                                                            </svg>
                                                                        @elseif ($isEntrada)
                                                                            <!-- Ícone de SETA PARA CIMA (verde) -->
                                                                            <svg width="24" height="24"
                                                                                viewBox="0 0 24 24" fill="none"
                                                                                xmlns="http://www.w3.org/2000/svg">
                                                                                <rect opacity="0.5" x="13" y="6"
                                                                                    width="13" height="2"
                                                                                    rx="1"
                                                                                    transform="rotate(90 13 6)"
                                                                                    fill="currentColor" />
                                                                                <path
                                                                                    d="M12.5657 8.56569L16.75 12.75C17.1642 13.1642
                                                                                    17.8358 13.1642 18.25 12.75C18.6642 12.3358
                                                                                    18.6642 11.6642 18.25 11.25L12.7071 5.70711C12.3166
                                                                                    5.31658 11.6834 5.31658 11.2929 5.70711L5.75 11.25C5.33579
                                                                                    11.6642 5.33579 12.3358 5.75 12.75C6.16421 13.1642
                                                                                    6.83579 13.1642 7.25 12.75L11.4343 8.56569C11.7467
                                                                                    8.25327 12.2533 8.25327 12.5657 8.56569Z"
                                                                                    fill="currentColor" />
                                                                            </svg>
                                                                        @else
                                                                            <!-- Ícone de SETA PARA BAIXO (vermelho) -->
                                                                            <svg width="24" height="24"
                                                                                viewBox="0 0 24 24" fill="none"
                                                                                xmlns="http://www.w3.org/2000/svg">
                                                                                <rect opacity="0.5" x="11" y="18"
                                                                                    width="13" height="2"
                                                                                    rx="1"
                                                                                    transform="rotate(-90 11 18)"
                                                                                    fill="currentColor" />
                                                                                <path
                                                                                    d="M11.4343 15.4343L7.25 11.25C6.83579 10.8358
                                                                                    6.16421 10.8358 5.75 11.25C5.33579 11.6642
                                                                                    5.33579 12.3358 5.75 12.75L11.2929 18.2929C11.6834
                                                                                    18.6834 12.3166 18.6834 12.7071 18.2929L18.25 12.75C18.6642
                                                                                    12.3358 18.6642 11.6642 18.25 11.25C17.8358 10.8358
                                                                                    17.1642 10.8358 16.75 11.25L12.5657 15.4343C12.2533
                                                                                    15.7467 11.7467 15.7467 11.4343 15.4343Z"
                                                                                    fill="currentColor" />
                                                                            </svg>
                                                                        @endif
                                                                    </span>
                                                                    {{ $lp->type === 'ambos' ? 'Ambos' : ucfirst($lp->type) }}
                                                                </span>

                                                                <!--end::Label-->
                                                            </div>
                                                            <!--end::Label-->
                                                        </div>
                                                        <!--end::Info-->
                                                    </div>
                                                    <!--end::Section-->
                                                </div>
                                                <!--end::Item-->
                                                <!--begin::Separator-->
                                                <div class="separator separator-dashed my-3"></div>
                                                <!--end::Separator-->
                                            @endforeach
                                        </div>
                                        <!--end::Items-->
                                    </div>
                                    <!--end::Body-->
                                    </divß>
                                    <!--end::LIst widget 8-->
                                </div>
                                <!--end::Col-->
                            </div>
                            <!--end::Sidebar-->
                        </div>
                        <!--end::Careers main-->
                    </div>
                    <!--end::Content container-->
                </div>
                <!--end::Content-->
            </div>
            <!--end::Content wrapper-->
</x-tenant-app-layout>
