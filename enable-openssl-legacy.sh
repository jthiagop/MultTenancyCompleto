#!/bin/bash

# Script para habilitar o provedor legacy no OpenSSL 3.x
# Isso permite que certificados A1 antigos com algoritmos RC2, 3DES sejam lidos

OPENSSL_CONFIG="/opt/homebrew/etc/openssl@3/openssl.cnf"
BACKUP_FILE="/opt/homebrew/etc/openssl@3/openssl.cnf.backup_$(date +%Y%m%d_%H%M%S)"

echo "=================================================="
echo "Habilitando Provedor Legacy no OpenSSL 3.x"
echo "=================================================="
echo ""

# Verificar se o arquivo existe
if [ ! -f "$OPENSSL_CONFIG" ]; then
    echo "❌ Erro: Arquivo de configuração não encontrado: $OPENSSL_CONFIG"
    exit 1
fi

# Fazer backup
echo "📦 Criando backup do arquivo original..."
sudo cp "$OPENSSL_CONFIG" "$BACKUP_FILE"
echo "✅ Backup criado: $BACKUP_FILE"
echo ""

# Verificar se o legacy já está configurado
if grep -q "^\[legacy_sect\]" "$OPENSSL_CONFIG"; then
    echo "⚠️  A seção [legacy_sect] já existe no arquivo."
    echo "   Verifique se 'activate = 1' está presente."
    exit 0
fi

# Criar arquivo temporário com as configurações
TEMP_FILE=$(mktemp)

# Ler o arquivo atual e modificar
awk '
BEGIN { 
    provider_sect_found = 0
    default_sect_found = 0
    legacy_added = 0
}

# Detectar a seção [provider_sect]
/^\[provider_sect\]/ {
    provider_sect_found = 1
    print $0
    next
}

# Se estamos na seção provider_sect e encontramos default = default_sect
/^default = default_sect/ && provider_sect_found && !legacy_added {
    print $0
    print "legacy = legacy_sect"
    legacy_added = 1
    next
}

# Detectar a seção [default_sect]
/^\[default_sect\]/ {
    default_sect_found = 1
    provider_sect_found = 0
    print $0
    next
}

# Se estamos na seção default_sect, ativar o provedor
/^# activate = 1/ && default_sect_found {
    print "activate = 1"
    next
}

# Adicionar a seção legacy_sect após a seção default_sect
/^####################################################################/ && default_sect_found {
    default_sect_found = 0
    print ""
    print "[legacy_sect]"
    print "activate = 1"
    print ""
    print $0
    next
}

# Imprimir todas as outras linhas normalmente
{ print $0 }
' "$OPENSSL_CONFIG" > "$TEMP_FILE"

# Substituir o arquivo original
echo "🔧 Aplicando configurações..."
sudo cp "$TEMP_FILE" "$OPENSSL_CONFIG"
rm "$TEMP_FILE"

echo "✅ Configuração aplicada com sucesso!"
echo ""
echo "=================================================="
echo "Próximos Passos:"
echo "=================================================="
echo ""
echo "1. Reinicie seu servidor PHP/Laravel:"
echo "   - Se usando 'php artisan serve': Ctrl+C e execute novamente"
echo "   - Se usando Valet: valet restart"
echo "   - Se usando Sail: sail restart"
echo ""
echo "2. Teste o upload do certificado novamente"
echo ""
echo "3. Para reverter as alterações (se necessário):"
echo "   sudo cp $BACKUP_FILE $OPENSSL_CONFIG"
echo ""
echo "=================================================="
