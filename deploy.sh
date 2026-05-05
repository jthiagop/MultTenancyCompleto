#!/bin/bash

# ─────────────────────────────────────────────────────────────────────────────
# deploy.sh — Script de deploy do Dominus Sistema (prod / VPS)
#
# Uso:  ./deploy.sh [branch]
#       branch padrão: main
#
# Pré-requisitos na VPS:
#   - PHP >= 8.2, Composer, Node 20 (nvm), Git, Nginx
#   - Usuário da execução com permissão de escrita em storage e bootstrap/cache
#   - sudo sem senha para: systemctl reload nginx (ajuste /etc/sudoers se necessário)
# ─────────────────────────────────────────────────────────────────────────────

set -euo pipefail

BRANCH="${1:-main}"
APP_DIR="/var/www/html/MultTenancyCompleto"
REACT_DIR="${APP_DIR}/resources/js/react"

echo ""
echo "══════════════════════════════════════════════════════"
echo "  Dominus — Deploy  |  Branch: ${BRANCH}"
echo "  $(date '+%d/%m/%Y %H:%M:%S')"
echo "══════════════════════════════════════════════════════"

# ── 1. Entra no diretório do projeto ─────────────────────────────────────────
cd "$APP_DIR" || { echo "❌  Diretório não encontrado: $APP_DIR"; exit 1; }

# ── 2. Modo manutenção ON ─────────────────────────────────────────────────────
echo "⏸  Ativando modo manutenção..."
php artisan down --retry=10 --secret="deploy-token" 2>/dev/null || true

# ── 3. Git pull ───────────────────────────────────────────────────────────────
echo "📥  Atualizando código (git pull origin ${BRANCH})..."
git fetch origin
git checkout "$BRANCH"
git pull origin "$BRANCH"

# ── 4. Dependências PHP ───────────────────────────────────────────────────────
echo "📦  Instalando dependências PHP..."
composer install --no-dev --no-interaction --optimize-autoloader --prefer-dist

# ── 5. Build React/Vite ───────────────────────────────────────────────────────
echo "⚙️   Instalando dependências Node e fazendo build do React..."

# Carrega NVM caso exista
export NVM_DIR="${HOME}/.nvm"
# shellcheck source=/dev/null
[ -s "${NVM_DIR}/nvm.sh" ] && . "${NVM_DIR}/nvm.sh"

cd "$REACT_DIR"
nvm use 2>/dev/null || true          # usa a versão do .nvmrc (20.19.4)
node --version
npm --version

npm ci --prefer-offline
npm run build                        # tsc && vite build

cd "$APP_DIR"

# ── 6. Migrações ──────────────────────────────────────────────────────────────
echo "🗃   Rodando migrações (central)..."
php artisan migrate --force

echo "🗃   Rodando migrações (tenants)..."
php artisan tenants:migrate --force

# ── 7. Caches do Laravel ─────────────────────────────────────────────────────
echo "🚀  Limpando e reconstruindo caches..."
php artisan optimize:clear
php artisan config:cache
php artisan route:cache
php artisan view:cache

# ── 8. Permissões ─────────────────────────────────────────────────────────────
echo "🔒  Ajustando permissões..."
chmod -R ug+rwx storage bootstrap/cache

# ── 9. Filas ──────────────────────────────────────────────────────────────────
echo "🔄  Reiniciando workers de fila..."
php artisan queue:restart

# ── 10. Modo manutenção OFF ───────────────────────────────────────────────────
echo "▶️   Desativando modo manutenção..."
php artisan up

# ── 11. Reload do Nginx ───────────────────────────────────────────────────────
echo "🌐  Recarregando Nginx..."
sudo systemctl reload nginx

echo ""
echo "══════════════════════════════════════════════════════"
echo "  ✅  Deploy concluído com sucesso!"
echo "  $(date '+%d/%m/%Y %H:%M:%S')"
echo "══════════════════════════════════════════════════════"
echo ""
