<!-- Mensagem de sucesso -->
@if (session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"
            aria-label="Fechar"></button>
    </div>
@endif

<!-- Mensagem de erro geral (não relacionada à validação) -->
@if (session('error'))
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        {{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"
            aria-label="Fechar"></button>
    </div>
@endif

<!-- Mensagens de erro de validação (caso existam) -->
@if ($errors->any())
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <ul>
            @foreach ($errors->all() as $erro)
                <li>{{ $erro }}</li>
            @endforeach
        </ul>
        <button type="button" class="btn-close" data-bs-dismiss="alert"
            aria-label="Fechar"></button>
    </div>
@endif
