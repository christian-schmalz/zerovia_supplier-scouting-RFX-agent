#!/bin/bash
set -e

cd "zerovia-sourcing-repo 3"

# Install PHP dependencies
composer install --no-interaction

# Install and build frontend
npm install
npm run build

# Create required Laravel directories
mkdir -p bootstrap/cache storage/framework/{sessions,views,cache} storage/logs
chmod -R 777 bootstrap/cache storage

# Create missing Laravel scaffolding if needed
if [ ! -f public/index.php ]; then
cat > public/index.php << 'PHP'
<?php
use Illuminate\Http\Request;
define('LARAVEL_START', microtime(true));
if (file_exists($maintenance = __DIR__.'/../storage/framework/maintenance.php')) {
    require $maintenance;
}
require __DIR__.'/../vendor/autoload.php';
(require_once __DIR__.'/../bootstrap/app.php')
    ->handleRequest(Request::capture());
PHP
fi

if [ ! -f bootstrap/providers.php ]; then
cat > bootstrap/providers.php << 'PHP'
<?php
return [
    App\Providers\AppServiceProvider::class,
    App\Providers\Filament\AdminPanelProvider::class,
];
PHP
fi

if [ ! -f app/Http/Controllers/Controller.php ]; then
cat > app/Http/Controllers/Controller.php << 'PHP'
<?php
namespace App\Http\Controllers;
abstract class Controller {}
PHP
fi

# Setup environment
if [ ! -f .env ]; then
cat > .env << 'ENV'
APP_NAME=ZEROvia
APP_ENV=local
APP_KEY=
APP_DEBUG=true
APP_URL=http://localhost:8000

DB_CONNECTION=pgsql
DB_HOST=db
DB_PORT=5432
DB_DATABASE=zerovia
DB_USERNAME=zerovia
DB_PASSWORD=zerovia

CACHE_STORE=file
SESSION_DRIVER=file
QUEUE_CONNECTION=sync
MAIL_MAILER=log
ENV
fi

# Wait for PostgreSQL to be ready
echo "Waiting for PostgreSQL..."
for i in {1..30}; do
  pg_isready -h db -p 5432 -U zerovia && break
  sleep 1
done

# Laravel setup
php artisan key:generate --force
php artisan migrate --force
php artisan db:seed --force

echo ""
echo "================================================"
echo "  ZEROvia Sourcing is ready!"
echo "  Login: admin@zerovia.ch / zerovia2026!"
echo "================================================"
