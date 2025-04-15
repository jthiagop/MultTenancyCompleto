{{-- resources/views/components/fullscreen-modal.blade.php --}}

@props([
    'id',                               // Required: ID único para o modal (para JS e ARIA)
    'title',                            // Required: Título exibido no cabeçalho
    'formAction' => '#',                // Optional: URL para onde o form será submetido
    'formId' => $id . 'Form',           // Optional: ID do formulário interno (útil para JS e botões fora do form)
    'formMethod' => 'POST',             // Optional: Método HTTP do formulário
    'hasCsrf' => true,                  // Optional: Incluir automaticamente o token CSRF?
    'footerButtons' => true             // Optional: Exibir os botões padrão Cancelar/Salvar no rodapé?
])

<div class="modal fade" id="{{ $id }}" tabindex="-1" aria-labelledby="{{ $id }}Label" aria-hidden="true">
    {{-- Adicionando a classe modal-fullscreen como solicitado --}}
    <div class="modal-dialog modal-fullscreen">
        <div class="modal-content">
            {{-- O formulário agora envolve o conteúdo --}}
            <form id="{{ $formId }}" action="{{ $formAction }}" method="{{ strtoupper($formMethod) === 'GET' ? 'GET' : 'POST' }}" class="form">
                {{-- Inclui CSRF automaticamente para métodos não-GET se hasCsrf for true --}}
                @if($hasCsrf && strtoupper($formMethod) !== 'GET')
                    @csrf
                    <meta name="csrf-token" content="{{ csrf_token() }}">
                @endif

                <div class="modal-header">
                    <h2 class="fw-bolder" id="{{ $id }}Label">{{ $title }}</h2>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                {{-- O conteúdo principal (seu formulário antigo) será injetado aqui através do $slot --}}
                <div class="modal-body scroll-y px-10 px-lg-15 pb-15 bg-light pt-5">
                    {{ $slot }}
                </div>
                <div class="modal-footer flex-center me-10"> {{-- Usando flex-center para alinhar --}}
                    {{-- Permite injetar um rodapé completamente customizado --}}
                    {{ $footer ?? '' }}

                    {{-- Exibe botões padrão se nenhum rodapé customizado ($footer) foi passado E $footerButtons é true --}}
                    @if (!isset($footer) && $footerButtons)
                        <button type="reset" class="btn btn-light me-3" data-bs-dismiss="modal">Cancelar</button>

                        {{-- Botão submit padrão - você pode adicionar o split button aqui ou via slot $footer --}}
                        <button type="submit" class="btn btn-primary">
                            <span class="indicator-label">Salvar</span>
                            <span class="indicator-progress">Aguarde...
                                <span class="spinner-border spinner-border-sm align-middle ms-2"></span></span>
                        </button>
                    @endif
                </div>
                </form>
            </div>
        </div>
    </div>