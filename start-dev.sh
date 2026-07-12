#!/bin/bash
# Start development environment
echo "Starting development environment..."

# Start Docker services
docker start bagisto-mysql 2>/dev/null || docker run -d \
  --name bagisto-mysql \
  -p 3306:3306 \
  -e MYSQL_ROOT_PASSWORD=root123 \
  -e MYSQL_DATABASE=bagisto \
  -e MYSQL_USER=bagisto \
  -e MYSQL_PASSWORD=bagisto123 \
  -v bagisto_mysql_data:/var/lib/mysql \
  mysql:latest

docker start bagisto-redis 2>/dev/null || docker run -d \
  --name bagisto-redis \
  -p 6379:6379 \
  redis:7

docker start bagisto-mailpit 2>/dev/null || docker run -d \
  --name bagisto-mailpit \
  -p 8025:8025 \
  -p 1025:1025 \
  axllent/mailpit

echo "✓ MySQL on 127.0.0.1:3306"
echo "✓ Redis on 127.0.0.1:6379"
echo "✓ Mailpit on http://localhost:8025 (email inbox)"
echo ""

# Start PHP dev server (PHP 8.3 required — B2B Suite)
cd my-store
echo "Starting Bagisto..."
echo ""
echo "  Store: http://localhost:8001"
echo "  Admin: http://localhost:8001/admin"
echo "  Login: ahmad13faizan@gmail.com / admin123"
echo ""
echo "Press Ctrl+C to stop the server."
echo ""
php8.3 artisan serve --port=8001
