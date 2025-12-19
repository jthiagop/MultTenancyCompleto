# Como Iniciar o Servidor Laravel para Acesso Mobile

## Problema
O app mobile precisa acessar o servidor Laravel pela rede local. Por padrão, o `php artisan serve` só aceita conexões de `localhost`.

## Solução

### 1. Iniciar o servidor na interface de rede (0.0.0.0)

Execute o comando abaixo para que o servidor aceite conexões de qualquer IP na rede local:

```bash
cd projeto-financeiro-web
php artisan serve --host=0.0.0.0 --port=8001
```

### 2. Verificar o IP da sua máquina

```bash
ifconfig | grep "inet " | grep -v 127.0.0.1
```

Ou no macOS:
```bash
ipconfig getifaddr en0
```

### 3. Configurar no App Mobile

No app mobile, ao configurar o tenant:
1. Digite o código de acesso (ex: DOM-ABC12)
2. Abra "Configurações Avançadas"
3. Digite o IP da sua máquina (ex: 192.168.1.2)
4. Clique em "Verificar e Continuar"

### 4. Verificar Firewall

Certifique-se de que o firewall não está bloqueando a porta 8001:

**macOS:**
- Sistema > Configurações > Rede > Firewall
- Permitir conexões de entrada na porta 8001

**Linux:**
```bash
sudo ufw allow 8001/tcp
```

### 5. Testar a Conexão

Teste se o servidor está acessível:

```bash
curl -X POST http://192.168.1.2:8001/api/tenant/by-code \
  -H "Content-Type: application/json" \
  -d '{"code":"SEU_CODIGO_AQUI"}'
```

Se retornar JSON (mesmo que seja erro 404), o servidor está acessível.

## Troubleshooting

### Erro: "Network Error"
- Verifique se o servidor está rodando com `--host=0.0.0.0`
- Verifique se o IP está correto
- Verifique se o firewall está permitindo conexões
- Certifique-se de que o celular e o computador estão na mesma rede Wi-Fi

### Erro: "Connection refused"
- O servidor não está rodando ou não está na porta 8001
- Verifique com: `lsof -i :8001`

### Erro: "Timeout"
- O IP pode estar incorreto
- Verifique se há firewall bloqueando
- Tente pingar o IP do servidor do celular





