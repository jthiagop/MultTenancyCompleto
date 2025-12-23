# Problema com Certificado A1 e OpenSSL 3.x

## Descrição do Problema

O erro `error:0308010C:digital envelope routines::unsupported` ocorre quando certificados PKCS12 (`.pfx` ou `.p12`) mais antigos tentam ser lidos pelo OpenSSL 3.x. Esses certificados usam algoritmos de criptografia considerados "legacy" (antigos/inseguros) que foram desabilitados por padrão no OpenSSL 3.0+.

### Erros Comuns Relacionados:
- `error:0308010C:digital envelope routines::unsupported`
- `error:11800071:PKCS12 routines::mac verify failure`
- NFePHP: `O certificado não pode ser lido!! Senha errada ou arquivo corrompido ou formato inválido!!`

## Por que acontece?

Certificados A1 mais antigos frequentemente usam:
- **RC2-40-CBC** ou **RC2-64-CBC** para criptografia
- **3DES** com MAC fraco
- Outros algoritmos deprecated no OpenSSL 3.x

## Soluções

### Solução 1: Habilitar Provedor Legacy no OpenSSL (RECOMENDADO PARA DESENVOLVIMENTO)

#### macOS (via Homebrew)

1. Localizar o arquivo de configuração do OpenSSL:
```bash
openssl version -d
# Saída exemplo: OPENSSLDIR: "/opt/homebrew/etc/openssl@3"
```

2. Editar o arquivo `openssl.cnf`:
```bash
sudo nano /opt/homebrew/etc/openssl@3/openssl.cnf
```

3. Adicionar ou modificar a seção `[provider_sect]`:
```ini
[default]
config_diagnostics = 1

[openssl_init]
providers = provider_sect

[provider_sect]
default = default_sect
legacy = legacy_sect

[default_sect]
activate = 1

[legacy_sect]
activate = 1
```

4. Reiniciar o servidor PHP/Laravel:
```bash
# Se estiver usando Laravel Sail
sail restart

# Se estiver usando php artisan serve
# Ctrl+C para parar e executar novamente:
php artisan serve

# Se estiver usando Valet
valet restart
```

#### Linux (Ubuntu/Debian)

1. Localizar configuração:
```bash
openssl version -d
# Geralmente: /usr/lib/ssl ou /etc/ssl
```

2. Editar `/etc/ssl/openssl.cnf` ou `/usr/lib/ssl/openssl.cnf`
```bash
sudo nano /etc/ssl/openssl.cnf
```

3. Aplicar as mesmas configurações acima

4. Reiniciar PHP-FPM ou servidor:
```bash
sudo systemctl restart php8.2-fpm
# ou
sudo systemctl restart nginx
```

### Solução 2: Converter o Certificado (RECOMENDADO PARA PRODUÇÃO)

Converter o certificado para usar algoritmos modernos:

```bash
# Extrair chave privada e certificado
openssl pkcs12 -in certificado_antigo.pfx -nocerts -out private_key.pem -nodes
openssl pkcs12 -in certificado_antigo.pfx -clcerts -nokeys -out certificate.pem

# Reempacotá-los com algoritmos modernos
openssl pkcs12 -export -out certificado_novo.pfx \
  -inkey private_key.pem \
  -in certificate.pem \
  -keypbe AES-256-CBC \
  -certpbe AES-256-CBC \
  -macalg SHA256

# Limpar arquivos temporários
rm private_key.pem certificate.pem
```

**IMPORTANTE**: Após converter, teste o novo certificado antes de deletar o antigo!

### Solução 3: Solicitar Novo Certificado

Entre em contato com sua Autoridade Certificadora (AC) e solicite um certificado mais recente que use algoritmos modernos.

## Verificando a Versão do OpenSSL

```bash
openssl version
# OpenSSL 3.x.x indica que você precisa de uma das soluções acima
```

## Testando o Certificado

Após aplicar qualquer solução, teste:

```bash
openssl pkcs12 -in seu_certificado.pfx -info -noout
# Se funcionar sem erros, o certificado está OK
```

## Para o Desenvolvedor

O código já foi atualizado em `app/Http/Controllers/App/NotaFiscalController.php` para:

1. ✅ Tentar múltiplas abordagens de leitura do certificado
2. ✅ Detectar erros específicos de OpenSSL 3.x
3. ✅ Fornecer mensagens de erro claras ao usuário
4. ✅ Logar informações detalhadas para debug

### Logs a Observar

No arquivo `storage/logs/laravel.log`, procure por:
- `Tentativa 1: OpenSSL direto da memória`
- `Tentativa 2: OpenSSL com arquivo temporário`
- `Tentativa 3: NFePHP`
- `Certificado com algoritmos legacy detectado`

## Referências

- [OpenSSL 3.0 Migration Guide](https://www.openssl.org/docs/man3.0/man7/migration_guide.html)
- [OpenSSL Legacy Provider](https://www.openssl.org/docs/man3.0/man7/OSSL_PROVIDER-legacy.html)
- [NFePHP Documentation](https://github.com/nfephp-org/nfephp)
