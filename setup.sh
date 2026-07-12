#!/bin/bash
# =============================================================
# Local Setup: Bagisto + Custom Modules
# Your system already has: MySQL 8, Node 22, Docker, Git
# Only needs: PHP 8.2 + Composer
#
# Run:  chmod +x setup.sh && ./setup.sh
# =============================================================

set -e

echo "========================================="
echo "  Bagisto Local Setup"
echo "  (PHP + Composer + Bagisto + Modules)"
echo "========================================="
echo ""

# --- Step 1: Install PHP 8.2 ---
if command -v php &> /dev/null; then
    echo "✓ PHP already installed: $(php -v | head -1)"
else
    echo "[1/4] Installing PHP 8.2 and extensions..."
    sudo add-apt-repository ppa:ondrej/php -y
    sudo apt update -qq
    sudo apt install -y php8.2 php8.2-cli php8.2-common php8.2-mysql php8.2-xml \
      php8.2-curl php8.2-gd php8.2-mbstring php8.2-zip php8.2-bcmath \
      php8.2-intl php8.2-readline php8.2-tokenizer php8.2-fileinfo
    echo "✓ PHP installed: $(php -v | head -1)"
fi

# --- Step 2: Install Composer ---
if command -v composer &> /dev/null; then
    echo "✓ Composer already installed: $(composer --version)"
else
    echo "[2/4] Installing Composer..."
    curl -sS https://getcomposer.org/installer | php
    sudo mv composer.phar /usr/local/bin/composer
    echo "✓ Composer installed: $(composer --version)"
fi

# --- Step 3: Start MySQL + Redis via Docker ---
echo ""
echo "[3/4] Starting MySQL + Redis via Docker..."

# MySQL
if docker ps --format '{{.Names}}' | grep -q 'bagisto-mysql'; then
    echo "✓ MySQL container already running"
else
    docker rm -f bagisto-mysql 2>/dev/null || true
    docker run -d \
      --name bagisto-mysql \
      -p 3306:3306 \
      -e MYSQL_ROOT_PASSWORD=root123 \
      -e MYSQL_DATABASE=bagisto \
      -e MYSQL_USER=bagisto \
      -e MYSQL_PASSWORD=bagisto123 \
      -v bagisto_mysql_data:/var/lib/mysql \
      mysql:latest
    echo "Waiting for MySQL to be ready..."
    sleep 15
fi

# Redis
if docker ps --format '{{.Names}}' | grep -q 'bagisto-redis'; then
    echo "✓ Redis container already running"
else
    docker rm -f bagisto-redis 2>/dev/null || true
    docker run -d \
      --name bagisto-redis \
      -p 6379:6379 \
      redis:7
fi

# Verify MySQL connection
if mysql -h 127.0.0.1 -P 3306 -u bagisto -pbagisto123 -e "SELECT 1;" &>/dev/null; then
    echo "✓ MySQL running on 127.0.0.1:3306"
else
    echo "⚠ MySQL not ready yet. Wait 10 more seconds and try:"
    echo "  mysql -h 127.0.0.1 -P 3306 -u bagisto -pbagisto123 -e 'SELECT 1;'"
    echo "  Then re-run this script."
    exit 1
fi
echo "✓ Redis running on 127.0.0.1:6379"

# --- Step 4: Install Bagisto + modules ---
echo ""
echo "[4/4] Installing Bagisto (takes 2-5 minutes)..."
SCRIPT_DIR="$(cd "$(dirname "$0")" && pwd)"

composer create-project bagisto/bagisto my-store --no-interaction
cd my-store

# Copy custom modules
echo "Copying custom modules..."
mkdir -p packages/Agency
cp -r "${SCRIPT_DIR}/packages/Agency/SiteVisit" packages/Agency/
cp -r "${SCRIPT_DIR}/packages/Agency/ProductSample" packages/Agency/

# Register packages in composer.json autoload
php -r "
\$json = json_decode(file_get_contents('composer.json'), true);
\$json['autoload']['psr-4']['Agency\\\\SiteVisit\\\\'] = 'packages/Agency/SiteVisit/src/';
\$json['autoload']['psr-4']['Agency\\\\ProductSample\\\\'] = 'packages/Agency/ProductSample/src/';
file_put_contents('composer.json', json_encode(\$json, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
"
composer dump-autoload

echo ""
echo "✓ Bagisto installed with custom modules"
echo ""
echo "========================================="
echo "  NEXT: Run the Bagisto installer"
echo "========================================="
echo ""
echo "  cd my-store"
echo "  php artisan bagisto:install"
echo ""
echo "Use these database values when prompted:"
echo "  Driver:   mysql"
echo "  Host:     127.0.0.1"
echo "  Port:     3306"
echo "  Database: bagisto"
echo "  Username: bagisto"
echo "  Password: bagisto123"
echo ""
echo "After install completes, run:"
echo ""
echo "  # Register custom module providers"
echo "  # Add these lines to bootstrap/providers.php:"
echo "  #   Agency\SiteVisit\Providers\SiteVisitServiceProvider::class,"
echo "  #   Agency\SiteVisit\Providers\ModuleServiceProvider::class,"
echo "  #   Agency\ProductSample\Providers\ProductSampleServiceProvider::class,"
echo "  #   Agency\ProductSample\Providers\ModuleServiceProvider::class,"
echo ""
echo "  php artisan migrate"
echo "  php artisan config:clear"
echo "  php artisan serve"
echo ""
echo "Then visit:"
echo "  Store: http://localhost:8000"
echo "  Admin: http://localhost:8000/admin"
echo ""
echo "To stop MySQL later:  docker stop bagisto-mysql"
echo "To stop Redis later:  docker stop bagisto-redis"
echo "To restart both:      docker start bagisto-mysql bagisto-redis"
echo ""
