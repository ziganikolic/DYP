#!/bin/bash

# Tournament Bracket App - Deployment Preparation Script
# This script prepares your application for deployment

set -e

echo "🏆 Tournament Bracket App - Deployment Preparation"
echo "=================================================="
echo ""

# Check if composer is installed
if ! command -v composer &> /dev/null; then
    echo "❌ Error: Composer is not installed!"
    echo ""
    echo "Please install Composer first:"
    echo "  macOS: brew install composer"
    echo "  Linux: See https://getcomposer.org/download/"
    echo "  Windows: Download from https://getcomposer.org/Composer-Setup.exe"
    echo ""
    echo "Or use Docker:"
    echo "  docker run --rm -v \$(pwd):/app composer:latest install"
    exit 1
fi

# Check if npm is installed
if ! command -v npm &> /dev/null; then
    echo "❌ Error: npm is not installed!"
    echo ""
    echo "Please install Node.js and npm first:"
    echo "  Visit: https://nodejs.org/"
    exit 1
fi

# Check PHP version
PHP_VERSION=$(php -r 'echo PHP_VERSION;')
echo "✓ PHP Version: $PHP_VERSION"

if ! php -r 'exit(version_compare(PHP_VERSION, "8.2.0", ">=") ? 0 : 1);'; then
    echo "❌ Error: PHP 8.2 or higher is required!"
    echo "   Current version: $PHP_VERSION"
    exit 1
fi

echo ""
echo "📦 Installing Composer dependencies..."
composer install --no-interaction --prefer-dist --optimize-autoloader

echo ""
echo "📦 Installing npm dependencies..."
npm install

echo ""
echo "🔨 Building frontend assets..."
npm run build

echo ""
echo "✓ Checking if .env exists..."
if [ ! -f .env ]; then
    echo "  Creating .env from .env.example..."
    cp .env.example .env
    php artisan key:generate
else
    echo "  .env already exists"
fi

echo ""
echo "✓ Checking database..."
if [ ! -f database/database.sqlite ]; then
    echo "  Creating SQLite database..."
    touch database/database.sqlite
fi

echo ""
echo "🔄 Running migrations..."
php artisan migrate --force

echo ""
echo "📋 Validating composer.json and composer.lock..."
composer validate

echo ""
echo "✅ Deployment preparation complete!"
echo ""
echo "📝 Next steps:"
echo ""
echo "1. Review and commit changes:"
echo "   git status"
echo "   git add composer.lock package-lock.json public/build"
echo "   git commit -m 'Prepare for deployment'"
echo ""
echo "2. Push to repository:"
echo "   git push origin claude/laravel-tournament-bracket-app-011CUbZoHGdxfmUcofaPutEs"
echo ""
echo "3. Deploy on Laravel Cloud or run locally:"
echo "   Terminal 1: php artisan serve"
echo "   Terminal 2: php artisan reverb:start"
echo ""
echo "🚀 Happy deploying!"
