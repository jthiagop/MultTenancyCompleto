<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Teste PHPFlasher</title>
    <link href="{{ url('assets/plugins/global/plugins.bundle.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ url('assets/css/style.bundle.css') }}" rel="stylesheet" type="text/css" />
</head>
<body>
    <div class="container mt-10">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Teste do PHPFlasher - Debug</h3>
            </div>
            <div class="card-body">
                <h4>Clique nos botões para testar:</h4>
                
                <div class="mt-5">
                    <h5>Teste via Backend (Laravel):</h5>
                    <a href="{{ route('flasher.test', ['type' => 'success']) }}" class="btn btn-success me-2">
                        Testar Success
                    </a>
                    <a href="{{ route('flasher.test', ['type' => 'error']) }}" class="btn btn-danger me-2">
                        Testar Error
                    </a>
                    <a href="{{ route('flasher.test', ['type' => 'warning']) }}" class="btn btn-warning me-2">
                        Testar Warning
                    </a>
                    <a href="{{ route('flasher.test', ['type' => 'info']) }}" class="btn btn-info">
                        Testar Info
                    </a>
                </div>
                
                <div class="mt-5">
                    <h5>Teste via JavaScript (Frontend):</h5>
                    <button onclick="testToastr('success')" class="btn btn-success me-2">
                        Toastr Success
                    </button>
                    <button onclick="testToastr('error')" class="btn btn-danger me-2">
                        Toastr Error
                    </button>
                    <button onclick="testToastr('warning')" class="btn btn-warning me-2">
                        Toastr Warning
                    </button>
                    <button onclick="testToastr('info')" class="btn btn-info">
                        Toastr Info
                    </button>
                </div>
                
                <div class="mt-5">
                    <h5>Console Log (F12 para ver):</h5>
                    <div id="debug-info" class="bg-light p-5 rounded">
                        <pre id="debug-output"></pre>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="{{ url('assets/plugins/global/plugins.bundle.js') }}"></script>
    <script src="{{ url('assets/js/scripts.bundle.js') }}"></script>
    
    @flasher_render
    
    <script>
        const debugOutput = document.getElementById('debug-output');
        
        function log(message) {
            console.log(message);
            debugOutput.textContent += message + '\n';
        }
        
        log('=== DIAGNÓSTICO PHPFLASHER ===');
        log('1. Flasher disponível: ' + (typeof flasher !== 'undefined' ? 'SIM' : 'NÃO'));
        log('2. jQuery disponível: ' + (typeof jQuery !== 'undefined' ? 'SIM (' + jQuery.fn.jquery + ')' : 'NÃO'));
        log('3. Toastr disponível: ' + (typeof toastr !== 'undefined' ? 'SIM' : 'NÃO'));
        
        // Listar scripts carregados
        const scripts = Array.from(document.scripts).filter(s => s.src.includes('flasher'));
        log('4. Scripts Flasher carregados: ' + scripts.length);
        scripts.forEach((s, i) => log('   ' + (i+1) + '. ' + s.src));
        
        // Verificar se há notificações
        @if(session()->has('flasher::envelopes'))
            log('5. Notificações na sessão: SIM');
            log('   Detalhes: ' + JSON.stringify(@json(session('flasher::envelopes')), null, 2));
        @else
            log('5. Notificações na sessão: NÃO');
        @endif
        
        log('=== FIM DO DIAGNÓSTICO ===');
        
        // Função para testar toastr diretamente
        function testToastr(type) {
            log('\nTestando Toastr tipo: ' + type);
            
            if (typeof toastr === 'undefined') {
                alert('Toastr não está disponível!');
                log('ERRO: Toastr não disponível');
                return;
            }
            
            const messages = {
                success: 'Operação realizada com sucesso!',
                error: 'Ocorreu um erro!',
                warning: 'Atenção: verifique os dados!',
                info: 'Informação importante!'
            };
            
            toastr.options = {
                closeButton: true,
                progressBar: true,
                positionClass: 'toast-top-right',
                timeOut: 5000,
                extendedTimeOut: 1000,
                showMethod: 'fadeIn',
                hideMethod: 'fadeOut'
            };
            
            toastr[type](messages[type], 'Teste ' + type.toUpperCase());
            log('Toast disparado com sucesso!');
        }
    </script>
</body>
</html>
