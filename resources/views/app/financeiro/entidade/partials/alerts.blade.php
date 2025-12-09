                    <!-- Mensagens de sessão convertidas para toasts (hidden, serão processadas pelo JS) -->
                    @if (session('success'))
                        <div data-session-success="{{ session('success') }}" style="display: none;"></div>
                    @endif

                    @if (session('error'))
                        <div data-session-error="{{ session('error') }}" style="display: none;"></div>
                    @endif

                    @if (session('warning'))
                        <div data-session-warning="{{ session('warning') }}" style="display: none;"></div>
                    @endif

                    @if (session('info'))
                        <div data-session-info="{{ session('info') }}" style="display: none;"></div>
                    @endif

                    <!-- Mensagens de erro de validação (caso existam) -->
                    @if (isset($errors) && $errors->any())
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
