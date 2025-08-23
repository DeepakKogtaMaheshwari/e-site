#!/bin/bash

# B2Battle Production Deployment Script
# Run this script on your production server after uploading files

echo "🚀 Starting B2Battle Production Deployment..."

# Colors for output
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
NC='\033[0m' # No Color

# Function to print colored output
print_status() {
    echo -e "${BLUE}[INFO]${NC} $1"
}

print_success() {
    echo -e "${GREEN}[SUCCESS]${NC} $1"
}

print_warning() {
    echo -e "${YELLOW}[WARNING]${NC} $1"
}

print_error() {
    echo -e "${RED}[ERROR]${NC} $1"
}

# Check if we're in the right directory
if [ ! -f "composer.json" ]; then
    print_error "composer.json not found. Please run this script from the project root directory."
    exit 1
fi

print_status "Checking PHP version..."
php_version=$(php -v | head -n 1 | cut -d " " -f 2 | cut -d "." -f 1,2)
if [[ $(echo "$php_version >= 8.1" | bc -l) -eq 1 ]]; then
    print_success "PHP version $php_version is compatible"
else
    print_error "PHP 8.1 or higher is required. Current version: $php_version"
    exit 1
fi

# Check if Composer is installed
if ! command -v composer &> /dev/null; then
    print_error "Composer is not installed. Please install Composer first."
    exit 1
fi

print_status "Installing/updating Composer dependencies..."
composer install --no-dev --optimize-autoloader --no-interaction
if [ $? -eq 0 ]; then
    print_success "Composer dependencies installed"
else
    print_error "Failed to install Composer dependencies"
    exit 1
fi

# Copy environment file if it doesn't exist
if [ ! -f ".env.local" ]; then
    if [ -f ".env.prod" ]; then
        print_status "Copying .env.prod to .env.local..."
        cp .env.prod .env.local
        print_warning "Please edit .env.local with your production settings!"
    else
        print_error ".env.local not found and .env.prod template not available"
        exit 1
    fi
else
    print_success ".env.local already exists"
fi

# Set proper permissions
print_status "Setting file permissions..."
chmod -R 755 public/
chmod -R 775 var/
chmod -R 775 var/cache/
chmod -R 775 var/log/
chmod 600 .env.local

# Create necessary directories
print_status "Creating necessary directories..."
mkdir -p var/cache/prod
mkdir -p var/log
mkdir -p var/sessions
mkdir -p public/uploads
mkdir -p var/backups

# Clear and warm up cache
print_status "Clearing and warming up cache..."
php bin/console cache:clear --env=prod --no-debug
php bin/console cache:warmup --env=prod --no-debug

# Run database migrations
print_status "Running database migrations..."
php bin/console doctrine:migrations:migrate --no-interaction --env=prod
if [ $? -eq 0 ]; then
    print_success "Database migrations completed"
else
    print_warning "Database migrations failed or no migrations to run"
fi

# Load fixtures (only if needed for initial setup)
read -p "Do you want to load sample data fixtures? (y/N): " load_fixtures
if [[ $load_fixtures =~ ^[Yy]$ ]]; then
    print_status "Loading data fixtures..."
    php bin/console doctrine:fixtures:load --no-interaction --env=prod
    print_success "Fixtures loaded"
fi

# Optimize for production
print_status "Optimizing for production..."

# Dump autoloader
composer dump-autoload --optimize --no-dev

# Generate optimized Twig cache
php bin/console twig:lint templates/ --env=prod

# Validate configuration
print_status "Validating configuration..."
php bin/console lint:container --env=prod
php bin/console debug:config framework --env=prod > /dev/null

# Security check
if command -v symfony &> /dev/null; then
    print_status "Running security check..."
    symfony check:security
fi

# Set final permissions
print_status "Setting final permissions..."
chown -R www-data:www-data var/ 2>/dev/null || true
chown -R www-data:www-data public/uploads/ 2>/dev/null || true

# Create robots.txt for production
print_status "Creating robots.txt..."
cat > public/robots.txt << EOF
User-agent: *
Allow: /

# Sitemap
Sitemap: https://yourdomain.com/sitemap.xml

# Disallow admin areas
Disallow: /admin/
Disallow: /api/
Disallow: /_profiler/
Disallow: /var/
Disallow: /vendor/
EOF

# Create basic sitemap.xml
print_status "Creating basic sitemap.xml..."
cat > public/sitemap.xml << EOF
<?xml version="1.0" encoding="UTF-8"?>
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">
    <url>
        <loc>https://yourdomain.com/</loc>
        <changefreq>daily</changefreq>
        <priority>1.0</priority>
    </url>
    <url>
        <loc>https://yourdomain.com/products</loc>
        <changefreq>daily</changefreq>
        <priority>0.9</priority>
    </url>
    <url>
        <loc>https://yourdomain.com/contact</loc>
        <changefreq>monthly</changefreq>
        <priority>0.7</priority>
    </url>
</urlset>
EOF

# Final checks
print_status "Running final checks..."

# Check if web server can write to necessary directories
if [ ! -w "var/cache" ]; then
    print_warning "var/cache directory is not writable by web server"
fi

if [ ! -w "var/log" ]; then
    print_warning "var/log directory is not writable by web server"
fi

# Display important information
echo ""
print_success "🎉 B2Battle deployment completed successfully!"
echo ""
print_status "Important next steps:"
echo "1. Edit .env.local with your production database and API credentials"
echo "2. Configure your web server (Apache/Nginx) to point to the 'public' directory"
echo "3. Set up SSL certificate for HTTPS"
echo "4. Configure your domain DNS settings"
echo "5. Test the application thoroughly"
echo "6. Set up monitoring and backups"
echo ""
print_status "Production URLs to test:"
echo "- Homepage: https://yourdomain.com/"
echo "- Products: https://yourdomain.com/products"
echo "- Contact: https://yourdomain.com/contact"
echo "- Test Cart: https://yourdomain.com/test-cart"
echo ""
print_warning "Remember to:"
echo "- Update Razorpay credentials in .env.local"
echo "- Configure email settings"
echo "- Set up regular database backups"
echo "- Monitor error logs in var/log/"
echo ""
print_success "Happy gaming! 🎮⚔️"
