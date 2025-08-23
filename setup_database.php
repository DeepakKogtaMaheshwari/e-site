<?php
// Quick Database Setup Script for B2Battle
// Run this file to set up the database and load sample products

require_once 'vendor/autoload.php';

use Symfony\Component\Dotenv\Dotenv;

echo "🚀 B2Battle Database Setup\n";
echo "==========================\n\n";

// Load environment variables
$dotenv = new Dotenv();
$dotenv->load('.env');

// Check if we're in the right directory
if (!file_exists('bin/console')) {
    echo "❌ Error: Please run this script from the project root directory.\n";
    exit(1);
}

echo "📋 Setting up database...\n";

// Step 1: Create database
echo "1. Creating database...\n";
$output = shell_exec('php bin/console doctrine:database:create --if-not-exists 2>&1');
echo $output . "\n";

// Step 2: Run migrations
echo "2. Running database migrations...\n";
$output = shell_exec('php bin/console doctrine:migrations:migrate --no-interaction 2>&1');
echo $output . "\n";

// Step 3: Load fixtures
echo "3. Loading sample products...\n";
$output = shell_exec('php bin/console doctrine:fixtures:load --no-interaction 2>&1');
echo $output . "\n";

// Step 4: Clear cache
echo "4. Clearing cache...\n";
$output = shell_exec('php bin/console cache:clear 2>&1');
echo $output . "\n";

echo "✅ Database setup completed!\n";
echo "\n🎉 Your B2Battle site is ready!\n";
echo "Visit: http://localhost:8001/products\n";
echo "\n📦 Sample products loaded:\n";
echo "- Professional Computer Mouse\n";
echo "- Mechanical Keyboard RGB\n";
echo "- Professional Audio Headset\n";
echo "- Professional Monitor\n";
echo "- Ergonomic Office Chair\n";
echo "- Wireless Controller\n";
echo "\n🚀 Ready for production deployment!\n";
?>
