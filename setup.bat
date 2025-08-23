@echo off
echo 🚀 Setting up E-Commerce Site...
echo ==================================

REM Check if composer is installed
where composer >nul 2>nul
if %errorlevel% neq 0 (
    echo ❌ Composer is not installed. Please install Composer first.
    echo Visit: https://getcomposer.org/download/
    pause
    exit /b 1
)

REM Check PHP version
for /f "tokens=*" %%i in ('php -r "echo PHP_MAJOR_VERSION.'.'.PHP_MINOR_VERSION;"') do set PHP_VERSION=%%i
echo ✅ PHP version: %PHP_VERSION%

REM Install dependencies
echo 📦 Installing Composer dependencies...
composer install

REM Check if .env exists
if not exist .env (
    echo 📝 Creating .env file from template...
    copy .env.example .env
    echo ⚠️  Please edit .env file with your configuration:
    echo    - Set APP_SECRET to a secure random string
    echo    - Configure your Razorpay keys
    echo    - Update database settings if needed
) else (
    echo ✅ .env file already exists
)

REM Create var directory if it doesn't exist
if not exist var (
    mkdir var
    echo 📁 Created var directory
)

REM Set up database
echo 🗄️  Setting up database...
php bin/console doctrine:database:create --if-not-exists

REM Run migrations
echo 🔄 Running database migrations...
php bin/console doctrine:migrations:migrate --no-interaction

REM Load fixtures
echo 📊 Loading sample data...
php bin/console doctrine:fixtures:load --no-interaction

REM Clear cache
echo 🧹 Clearing cache...
php bin/console cache:clear

echo.
echo 🎉 Setup completed successfully!
echo ==================================
echo.
echo Next steps:
echo 1. Edit .env file with your configuration
echo 2. Start the development server:
echo    - Using Symfony CLI: symfony server:start
echo    - Using PHP: php -S localhost:8000 -t public/
echo 3. Visit http://localhost:8000 in your browser
echo.
echo For Razorpay integration:
echo 1. Sign up at https://razorpay.com/
echo 2. Get your test API keys from the dashboard
echo 3. Update RAZORPAY_KEY_ID and RAZORPAY_KEY_SECRET in .env
echo.
echo 📖 Read README.md for detailed documentation
echo 🚀 Read DEPLOYMENT.md for production deployment guide
echo.
pause
