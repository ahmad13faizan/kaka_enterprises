#!/bin/bash
# Stop development environment
echo "Stopping development environment..."

docker stop bagisto-mysql 2>/dev/null
docker stop bagisto-redis 2>/dev/null
docker stop bagisto-mailpit 2>/dev/null

echo "✓ MySQL stopped"
echo "✓ Redis stopped"
echo "✓ Mailpit stopped"
echo "✓ Done. Your data is preserved (will be there next time you start)."
