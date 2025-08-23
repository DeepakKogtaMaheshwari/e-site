#!/bin/bash

# E-Commerce Site Setup Script
# This script helps set up the Symfony e-commerce application

set -e

echo "🚀 Setting up E-Commerce Site..."
echo "=================================="

# Check if composer is installed
if ! command -v composer &> /dev/null; then
    echo "❌ Composer is not installed. Please install Composer first."
    echo "Visit: https://getcomposer.org/download/"
    exit 1
fi

# Check PHP version
PHP_VERSION=$(php -r "echo PHP_MAJOR_VERSION.'.'.PHP_MINOR_VERSION;")
REQUIRED_VERSION="8.2"

if [ "$(printf '%s\n' "$REQUIRED_VERSION" "$PHP_VERSION" | sort -V | head -n1)" != "$REQUIRED_VERSION" ]; then
    echo "❌ PHP $REQUIRED_VERSION or higher is required. Current version: $PHP_VERSION"
    exit 1
fi

echo "✅ PHP version check passed ($PHP_VERSION)"

# Install dependencies
echo "📦 Installing Composer dependencies..."
composer install

# Check if .env exists
if [ ! -f .env ]; then
    echo "📝 Creating .env file from template..."
    cp .env.example .env
    echo "⚠️  Please edit .env file with your configuration:"
    echo "   - Set APP_SECRET to a secure random string"
    echo "   - Configure your Razorpay keys"
    echo "   - Update database settings if needed"
else
    echo "✅ .env file already exists"
fi

# Create var directory if it doesn't exist
if [ ! -d var ]; then
    mkdir -p var
    echo "📁 Created var directory"
fi

# Set up database
echo "🗄️  Setting up database..."

# Check if database exists (for SQLite)
if [ ! -f var/data.db ]; then
    echo "Creating SQLite database..."
    php bin/console doctrine:database:create --if-not-exists
else
    echo "✅ Database already exists"
fi

# Run migrations
echo "🔄 Running database migrations..."
php bin/console doctrine:migrations:migrate --no-interaction

# Load fixtures
echo "📊 Loading sample data..."
php bin/console doctrine:fixtures:load --no-interaction

# Clear cache
echo "🧹 Clearing cache..."
php bin/console cache:clear

echo ""
echo "🎉 Setup completed successfully!"
echo "=================================="
echo ""
echo "Next steps:"
echo "1. Edit .env file with your configuration"
echo "2. Start the development server:"
echo "   - Using Symfony CLI: symfony server:start"
echo "   - Using PHP: php -S localhost:8000 -t public/"
echo "3. Visit http://localhost:8000 in your browser"
echo ""
echo "For Razorpay integration:"
echo "1. Sign up at https://razorpay.com/"
echo "2. Get your test API keys from the dashboard"
echo "3. Update RAZORPAY_KEY_ID and RAZORPAY_KEY_SECRET in .env"
echo ""
echo "📖 Read README.md for detailed documentation"
echo "🚀 Read DEPLOYMENT.md for production deployment guide"
