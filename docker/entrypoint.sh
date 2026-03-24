#!/usr/bin/env sh
set -e
cd /var/www/html

php artisan storage:link --force 2>/dev/null || true

exec php artisan serve --host=0.0.0.0 --port="${PORT:-8000}"
