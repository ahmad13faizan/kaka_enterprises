#!/bin/bash
# =============================================================
# Share Bagisto with QA over the public internet (FREE)
# Uses Cloudflare Tunnel — no account, no VPS, real HTTPS.
#
# Just run:  ./share-qa.sh
# It starts Docker services, the PHP server, and the tunnel.
# =============================================================

set -e
SCRIPT_DIR="$(cd "$(dirname "$0")" && pwd)"

echo "========================================="
echo "  Bagisto QA Share (Cloudflare Tunnel)"
echo "========================================="
echo ""

# 1. Start Docker services
echo "[1/3] Starting Docker services (MySQL, Redis, Mailpit)..."
docker start bagisto-mysql 2>/dev/null || echo "  (mysql not found — run ./setup.sh first)"
docker start bagisto-redis 2>/dev/null || true
docker start bagisto-mailpit 2>/dev/null || true
sleep 3
echo "  ✓ Services up"
echo ""

# 2. Start PHP server in background (bind to all interfaces)
echo "[2/3] Starting Bagisto server on port 8001..."
cd "${SCRIPT_DIR}/my-store"

# Kill any existing artisan serve on 8001
pkill -f "artisan serve.*8001" 2>/dev/null || true
sleep 1

php8.3 artisan serve --port=8001 --host=0.0.0.0 > /tmp/bagisto-serve.log 2>&1 &
SERVE_PID=$!
sleep 3
echo "  ✓ Server running (PID ${SERVE_PID})"
echo ""

# 3. Start the tunnel (foreground)
echo "[3/3] Opening public tunnel..."
echo ""
echo "  ╔════════════════════════════════════════════════════════╗"
echo "  ║  A public https://...trycloudflare.com URL will appear   ║"
echo "  ║  below. Share THAT link with your QA team.               ║"
echo "  ║                                                          ║"
echo "  ║  Admin login:    ahmad13faizan@gmail.com / admin123      ║"
echo "  ║  (append /admin to the URL for the admin panel)          ║"
echo "  ║                                                          ║"
echo "  ║  Press Ctrl+C to stop sharing.                           ║"
echo "  ╚════════════════════════════════════════════════════════╝"
echo ""

# Cleanup on exit
trap "echo ''; echo 'Stopping server...'; kill ${SERVE_PID} 2>/dev/null; echo 'Stopped sharing.'; exit 0" INT TERM

cloudflared tunnel --url http://localhost:8001
