#!/bin/bash

# Script para iniciar o localtunnel com subdomínio para desenvolvimento
# Uso: ./start-localtunnel.sh [subdomain] [port]
# Exemplo: ./start-localtunnel.sh recife 8000

SUBDOMAIN=${1:-recife}
PORT=${2:-8000}

echo "🚀 Iniciando localtunnel..."
echo "📡 Subdomínio: $SUBDOMAIN"
echo "🔌 Porta: $PORT"
echo ""
echo "⚠️  Certifique-se de que o servidor Laravel está rodando na porta $PORT"
echo ""

# Diretório do script
SCRIPT_DIR="$( cd "$( dirname "${BASH_SOURCE[0]}" )" && pwd )"
NODE_MODULES_BIN="$SCRIPT_DIR/node_modules/.bin"

# Verificar se o localtunnel está instalado globalmente
LT_CMD=""
if command -v lt &> /dev/null; then
    LT_CMD="lt"
elif command -v localtunnel &> /dev/null; then
    LT_CMD="localtunnel"
fi

# Se não encontrou globalmente, verificar localmente
if [ -z "$LT_CMD" ]; then
    if [ -f "$NODE_MODULES_BIN/lt" ]; then
        LT_CMD="$NODE_MODULES_BIN/lt"
    elif [ -f "$NODE_MODULES_BIN/localtunnel" ]; then
        LT_CMD="$NODE_MODULES_BIN/localtunnel"
    fi
fi

# Se ainda não encontrou, tentar instalar localmente
if [ -z "$LT_CMD" ]; then
    echo "⚠️  localtunnel não está instalado. Tentando instalar localmente..."
    echo ""
    
    if ! command -v npm &> /dev/null; then
        echo "❌ npm não está instalado!"
        echo ""
        echo "Para instalar localtunnel, você precisa do Node.js e npm."
        echo "Instale em: https://nodejs.org/"
        exit 1
    fi
    
    # Instalar localmente (não precisa de sudo)
    npm install localtunnel
    
    if [ $? -eq 0 ]; then
        if [ -f "$NODE_MODULES_BIN/lt" ]; then
            LT_CMD="$NODE_MODULES_BIN/lt"
        elif [ -f "$NODE_MODULES_BIN/localtunnel" ]; then
            LT_CMD="$NODE_MODULES_BIN/localtunnel"
        fi
    fi
fi

# Se ainda não encontrou, mostrar erro
if [ -z "$LT_CMD" ]; then
    echo "❌ Não foi possível instalar ou encontrar localtunnel!"
    echo ""
    echo "Tente instalar manualmente:"
    echo "  npm install localtunnel"
    echo ""
    echo "Ou globalmente (pode precisar de sudo):"
    echo "  sudo npm install -g localtunnel"
    echo ""
    exit 1
fi

echo "✅ localtunnel encontrado: $LT_CMD"
echo ""
echo "🌐 Seu túnel será criado em: https://$SUBDOMAIN.loca.lt"
echo ""
echo "📋 Configure o webhook no painel da Meta com:"
echo "   https://$SUBDOMAIN.loca.lt/whatsapp/webhook"
echo ""
echo "⏹️  Pressione Ctrl+C para parar o túnel"
echo ""

# Iniciar o localtunnel
$LT_CMD --port $PORT --subdomain $SUBDOMAIN

