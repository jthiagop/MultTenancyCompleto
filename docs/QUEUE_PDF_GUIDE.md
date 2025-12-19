# Guia Rápido: Implementação de PDF em Background

## ✅ O que foi criado:

### 1. Jobs (3 arquivos)
- `app/Jobs/GenerateReciboPdfJob.php`
- `app/Jobs/GenerateConciliacaoPdfJob.php`
- `app/Jobs/GenerateBoletimPdfJob.php`

### 2. Database
- Migration: `2025_12_19_162705_create_pdf_generations_table.php`
- Model: `app/Models/PdfGeneration.php`

### 3. BrowsershotHelper Otimizado
- Argumentos `--single-process`, `--no-zygote`
- Reduz consumo de memória em ~50%

---

## 🚀 Como Usar nos Controllers:

### Exemplo - Boletim Financeiro:

**ANTES (Síncrono - Trava servidor):**
```php
public function gerarPdf($mes, $ano) {
    // ... código ...
    $pdf = Browsershot::html($html)->pdf();
    return response($pdf); // ❌ Trava servidor
}
```

**DEPOIS (Assíncrono - Não trava):**
```php
use App\Jobs\GenerateBoletimPdfJob;
use App\Models\PdfGeneration;

public function gerarPdf(Request $request) {
    $mes = $request->mes;
    $ano = $request->ano;
    
    // Criar registro de rastreamento
    $pdfGen = PdfGeneration::create([
        'type' => 'boletim',
        'user_id' => auth()->id(),
        'company_id' => session('active_company_id'),
        'status' => 'pending',
        'parameters' => ['mes' => $mes, 'ano' => $ano],
    ]);
    
    // Despachar job
    GenerateBoletimPdfJob::dispatch($mes, $ano, session('active_company_id'), auth()->id())
        ->onQueue('pdfs');
    
    // Retornar ID para polling
    return response()->json([
        'success' => true,
        'pdf_id' => $pdfGen->id,
        'message' => 'PDF sendo gerado em background...'
    ]);
}

// Nova rota para verificar status
public function checkPdfStatus($id) {
    $pdfGen = PdfGeneration::findOrFail($id);
    
    return response()->json([
        'status' => $pdfGen->status,
        'download_url' => $pdfGen->download_url,
    ]);
}
```

---

## 📋 Passos para Deploy:

### 1. Rodar Migration
```bash
php artisan migrate
```

### 2. Configurar Queue no .env
```env
QUEUE_CONNECTION=database
# OU para melhor performance:
QUEUE_CONNECTION=redis
```

### 3. Criar tabela de jobs (se não existir)
```bash
php artisan queue:table
php artisan migrate
```

### 4. Configurar Supervisor
```bash
sudo nano /etc/supervisor/conf.d/laravel-worker.conf
```

Conteúdo:
```ini
[program:laravel-worker]
process_name=%(program_name)s_%(process_num)02d
command=php /var/www/html/MultTenancyCompleto/artisan queue:work --sleep=3 --tries=3 --max-time=3600 --queue=pdfs,default
autostart=true
autorestart=true
user=www-data
numprocs=2
redirect_stderr=true
stdout_logfile=/var/www/html/MultTenancyCompleto/storage/logs/worker.log
stopwaitsecs=3600
```

```bash
sudo supervisorctl reread
sudo supervisorctl update
sudo supervisorctl start laravel-worker:*
```

### 5. Verificar Worker
```bash
sudo supervisorctl status
php artisan queue:work --once # Teste manual
```

---

## 🎨 Frontend - Polling Example:

```javascript
function gerarPDF() {
    fetch('/boletim/gerar', {
        method: 'POST',
        headers: {'Content-Type': 'application/json'},
        body: JSON.stringify({mes: 12, ano: 2024})
    })
    .then(r => r.json())
    .then(data => {
        // Mostrar loading
        Swal.fire({
            title: 'Gerando PDF...',
            text: 'Aguarde enquanto processamos seu relatório',
            allowOutsideClick: false,
            didOpen: () => Swal.showLoading()
        });
        
        // Polling a cada 2 segundos
        const interval = setInterval(() => {
            fetch(`/pdf/status/${data.pdf_id}`)
                .then(r => r.json())
                .then(status => {
                    if (status.status === 'completed') {
                        clearInterval(interval);
                        Swal.close();
                        window.open(status.download_url, '_blank');
                    } else if (status.status === 'failed') {
                        clearInterval(interval);
                        Swal.fire('Erro', 'Falha ao gerar PDF', 'error');
                    }
                });
        }, 2000);
    });
}
```

---

## ⚡ Benefícios:

✅ Servidor não trava mais
✅ Usuário recebe resposta imediata
✅ PDFs grandes não causam timeout
✅ Retry automático em caso de falha
✅ Histórico de PDFs gerados
✅ Melhor experiência do usuário

---

## 🔧 Troubleshooting:

**Queue não processa:**
```bash
php artisan queue:failed # Ver jobs falhados
php artisan queue:retry all # Retentar todos
```

**Ver logs:**
```bash
tail -f storage/logs/laravel.log
tail -f storage/logs/worker.log
```

**Limpar jobs travados:**
```bash
php artisan queue:flush
```
