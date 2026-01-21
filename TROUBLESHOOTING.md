# 🚨 Troubleshooting Rápido

## Erro: "Unexpected token '<'"

```
❌ Problema: JSON começando com <br /><b> (HTML de erro)
✅ Solução:
   1. Verificar storage/logs/laravel.log
   2. Usar teste em public/teste-grafico-missas.html
   3. Aumentar memory_limit em php.ini (mínimo 256MB)
```

## Erro: "Broken pipe"

```
❌ Problema: Conexão interrompida no meio da resposta
✅ Solução:
   1. Aumentar max_execution_time em php.ini (mínimo 60s)
   2. Aumentar memory_limit em php.ini (mínimo 512MB)
   3. Testar com período menor (1 dia em vez de 1 ano)
   4. Criar índices SQL em database/migrations/
```

## Erro: "Conexão recusada"

```
❌ Problema: Servidor não está respondendo
✅ Solução:
   1. Verificar se Laravel está rodando: php artisan serve
   2. Verificar porta (padrão 8000)
   3. Verificar firewall
```

## Gráfico fica em branco

```
❌ Problema: Dados vazios ou ApexCharts não carregou
✅ Solução:
   1. Verificar console (F12)
   2. Verificar se há dados: SELECT COUNT(*) FROM bank_statements WHERE conciliado_com_missa = 1
   3. Tentar período diferente
   4. Limpar cache: php artisan cache:clear
```

## Performance lenta (>5 segundos)

```
❌ Problema: Query ou processamento demorado
✅ Solução (em ordem de efetividade):
   1. Criar índices SQL (50-80% mais rápido)
   2. Implementar cache (90% mais rápido)
   3. Aumentar memory_limit (para evitar swapping)
   4. Usar aggregation em vez de PHP (100x mais rápido)
```

## Erro: 500 (Internal Server Error)

```
❌ Problema: Exceção no servidor
✅ Solução:
   1. Verificar logs: tail -f storage/logs/laravel.log
   2. Procurar por "getMissasChartData"
   3. Implementar as correções sugeridas no erro
```

## Erro: 401 (Unauthorized)

```
❌ Problema: Usuário não autenticado
✅ Solução:
   1. Fazer login na aplicação
   2. Verificar se user.active está true
   3. Verificar se email foi verificado
```

## Erro: 403 (Forbidden)

```
❌ Problema: Usuário sem permissão
✅ Solução:
   1. Verificar permissões em app/Http/Controllers/App/DashboardController.php
   2. Verificar middleware 'check.user.active'
   3. Verificar session('active_company_id')
```

---

## 🔧 Comandos Úteis

### Limpar Cache
```bash
php artisan cache:clear
php artisan config:clear
php artisan view:clear
php artisan optimize:clear
```

### Verificar Logs
```bash
# Últimas 100 linhas
tail -n 100 storage/logs/laravel.log

# Seguir em tempo real
tail -f storage/logs/laravel.log

# Buscar erros
grep -i "error\|exception" storage/logs/laravel.log
```

### Testar Banco de Dados
```sql
-- Contar registros
SELECT COUNT(*) FROM bank_statements WHERE conciliado_com_missa = 1;

-- Ver volume por empresa
SELECT company_id, COUNT(*) FROM bank_statements 
WHERE conciliado_com_missa = 1 
GROUP BY company_id 
ORDER BY COUNT(*) DESC;

-- Ver índices
SHOW INDEX FROM bank_statements;
```

### Testar Laravel
```bash
# Iniciar servidor
php artisan serve

# Usar tinker
php artisan tinker

# Migrar banco
php artisan migrate

# Seed banco
php artisan db:seed
```

---

## 📱 DevTools - Atalhos Úteis

| Ação | Atalho |
|------|--------|
| Abrir DevTools | F12 ou Ctrl+Shift+I |
| Console | F12 → Console |
| Network | F12 → Network |
| Performance | F12 → Performance |
| Storage | F12 → Application → Storage |
| Limpar Cache | F12 → Application → Clear Site Data |

### Testar API no Console
```javascript
// Copiar e colar no console (F12)

// Teste rápido
fetch('/dashboard/missas-chart-data', {
    headers: {'Accept': 'application/json'}
})
.then(r => r.json())
.then(d => console.log('✅', d))
.catch(e => console.error('❌', e.message))

// Com datas específicas
fetch('/dashboard/missas-chart-data?start_date=2026-01-01&end_date=2026-01-21', {
    headers: {'Accept': 'application/json'}
})
.then(r => {
    console.log('Status:', r.status);
    console.log('Content-Type:', r.headers.get('content-type'));
    return r.json();
})
.then(d => console.log('Data:', d))
.catch(e => console.error('Error:', e))
```

---

## 🐛 Debug Avançado

### Ativar Query Logging

**config/database.php:**
```php
'connections' => [
    'mysql' => [
        // ... outras configurações
        'log' => env('DB_LOG', false),
    ],
],
```

**Usar em Controller:**
```php
DB::enableQueryLog();
// ... suas queries
dd(DB::getQueryLog());
```

### Ativar Laravel Debugbar

```bash
composer require barryvdh/laravel-debugbar --dev
```

Acesso: `http://localhost:8000` → Debugbar no rodapé

### Monitorar Requisições com Postman

1. Abrir Postman
2. Nova requisição GET
3. URL: `http://localhost:8000/dashboard/missas-chart-data`
4. Headers: `Accept: application/json`, `X-Requested-With: XMLHttpRequest`
5. Send
6. Verificar Status, Headers e Body

---

## ✅ Checklist de Diagnóstico

- [ ] Verificar `storage/logs/laravel.log`
- [ ] Testar com `public/teste-grafico-missas.html`
- [ ] Verificar `memory_limit` do PHP
- [ ] Verificar `max_execution_time` do PHP
- [ ] Testar com período menor (1 dia)
- [ ] Criar índices SQL
- [ ] Limpar cache Laravel
- [ ] Verificar dados no banco
- [ ] Usar DevTools do navegador
- [ ] Testar com cURL

---

## 📞 Informações de Contato

Se persistir o problema após todas essas verificações, forneça:

1. **Logs completos** de `storage/logs/laravel.log`
2. **Versão do PHP**: `php -v`
3. **Versão do Laravel**: `php artisan --version`
4. **Saída do teste HTML**: Print da página `public/teste-grafico-missas.html`
5. **Browser e versão** utilizado
6. **Volume de dados**: `SELECT COUNT(*) FROM bank_statements`

---

**Última atualização:** 21 de janeiro de 2026
**Versão:** 1.0
