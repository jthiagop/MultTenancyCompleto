# Configurações para Servidor - Geração de PDF

## 1. PHP-FPM (/etc/php/8.2/fpm/pool.d/www.conf)

```ini
; Aumentar timeout de execução
request_terminate_timeout = 300

; Aumentar memória
pm.max_children = 10
pm.start_servers = 3
pm.min_spare_servers = 2
pm.max_spare_servers = 5
pm.max_requests = 500
```

## 2. PHP.ini (/etc/php/8.2/fpm/php.ini e /etc/php/8.2/cli/php.ini)

```ini
max_execution_time = 300
memory_limit = 512M
upload_max_filesize = 50M
post_max_size = 50M
```

## 3. Nginx (/etc/nginx/sites-available/seu-site)

```nginx
location ~ \.php$ {
    fastcgi_pass unix:/var/run/php/php8.2-fpm.sock;
    fastcgi_index index.php;
    fastcgi_param SCRIPT_FILENAME $document_root$fastcgi_script_name;
    include fastcgi_params;
    
    # Aumentar timeouts
    fastcgi_read_timeout 300;
    fastcgi_send_timeout 300;
    fastcgi_connect_timeout 300;
    
    # Aumentar buffer
    fastcgi_buffer_size 128k;
    fastcgi_buffers 256 16k;
    fastcgi_busy_buffers_size 256k;
}

# Timeout geral
proxy_read_timeout 300;
proxy_connect_timeout 300;
proxy_send_timeout 300;
```

## 4. Cloudflare (se estiver usando)

- Ative o modo "Proxy" para "DNS only" nas rotas de PDF
- OU use subdomínio separado sem proxy do Cloudflare
- OU implemente geração em background

## 5. Comandos para aplicar

```bash
# Reiniciar PHP-FPM
sudo systemctl restart php8.2-fpm

# Reiniciar Nginx
sudo nginx -t
sudo systemctl restart nginx

# Verificar logs
sudo tail -f /var/log/nginx/error.log
sudo tail -f /var/log/php8.2-fpm.log
```

## 6. Configurar Queue Worker (Recomendado)

```bash
# Instalar Supervisor
sudo apt install supervisor

# Criar configuração
sudo nano /etc/supervisor/conf.d/laravel-worker.conf
```

Conteúdo:
```ini
[program:laravel-worker]
process_name=%(program_name)s_%(process_num)02d
command=php /var/www/html/MultTenancyCompleto/artisan queue:work --sleep=3 --tries=3 --max-time=3600
autostart=true
autorestart=true
stopasgroup=true
killasgroup=true
user=www-data
numprocs=2
redirect_stderr=true
stdout_logfile=/var/www/html/MultTenancyCompleto/storage/logs/worker.log
stopwaitsecs=3600
```

```bash
# Recarregar Supervisor
sudo supervisorctl reread
sudo supervisorctl update
sudo supervisorctl start laravel-worker:*
```

## 7. Laravel .env

```env
QUEUE_CONNECTION=database
# OU
QUEUE_CONNECTION=redis
```

## 8. Otimizações no BrowsershotHelper

Já implementado:
- ✅ --no-sandbox
- ✅ --disable-gpu
- ✅ --disable-dev-shm-usage

Adicionar se necessário:
- --single-process
- --disable-software-rasterizer
