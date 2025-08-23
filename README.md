# B2Battle - Premium Gaming Store (Symfony 7+)

A modern, production-ready gaming e-commerce website built with Symfony 7+ featuring cutting-edge UI design and Razorpay payment integration. Perfect for Razorpay account approval with professional gaming gear catalog.

## 🎮 Features

- **Gaming Product Catalog**: Professional gaming gear with detailed descriptions
- **Modern UI/UX**: Glassmorphism design with animations and gradients
- **Secure Payments**: Razorpay integration with enhanced checkout experience
- **Responsive Design**: Bootstrap 5 with custom modern styling
- **Professional Content**: Gaming-focused legal pages and support
- **Database**: SQLite (development) / MySQL/PostgreSQL (production)
- **SEO Optimized**: Professional metadata and structure for search engines

## Quick Start

### Prerequisites

- PHP 8.2 or higher
- Composer
- Web server (Apache/Nginx) or Symfony CLI

### Installation

1. **Clone/Download the project**
   ```bash
   # If using git
   git clone <repository-url>
   cd E-site
   
   # Or extract the downloaded files to your web directory
   ```

2. **Install dependencies**
   ```bash
   composer install
   ```

3. **Configure environment**
   ```bash
   cp .env.example .env
   ```
   
   Edit `.env` file and update:
   - `APP_SECRET`: Generate a secure secret key
   - `RAZORPAY_KEY_ID`: Your Razorpay test key ID
   - `RAZORPAY_KEY_SECRET`: Your Razorpay test key secret
   - `DATABASE_URL`: Database connection (SQLite by default)

4. **Set up database**
   ```bash
   # Create database and run migrations
   php bin/console doctrine:database:create
   php bin/console doctrine:migrations:migrate
   
   # Load sample products
   php bin/console doctrine:fixtures:load
   ```

5. **Start the application**
   ```bash
   # Using Symfony CLI (recommended)
   symfony server:start
   
   # Or using PHP built-in server
   php -S localhost:8000 -t public/
   ```

6. **Visit your site**
   Open http://localhost:8000 in your browser

## Razorpay Setup

1. **Get Razorpay Keys**
   - Sign up at https://razorpay.com/
   - Go to Dashboard → Settings → API Keys
   - Copy your Key ID and Key Secret

2. **Test Mode**
   - Use test keys (starting with `rzp_test_`)
   - Test payments won't charge real money
   - Use test card: 4111 1111 1111 1111

3. **Production Mode**
   - Replace test keys with live keys in `.env`
   - Complete KYC verification on Razorpay
   - Test thoroughly before going live

## Project Structure

```
├── config/                 # Symfony configuration
├── migrations/             # Database migrations
├── public/                 # Web root directory
├── src/
│   ├── Controller/         # Application controllers
│   ├── DataFixtures/       # Database fixtures
│   ├── Entity/             # Doctrine entities
│   └── Repository/         # Database repositories
├── templates/              # Twig templates
├── .env                    # Environment configuration
├── .env.example           # Environment template
└── composer.json          # Dependencies
```

## Available Routes

- `/` - Homepage
- `/products` - Product listing
- `/product/{id}` - Product details
- `/checkout/{id}` - Checkout page
- `/payment-success` - Payment success page
- `/payment-failed` - Payment failure page
- `/privacy-policy` - Privacy policy
- `/terms-and-conditions` - Terms & conditions
- `/refund-policy` - Refund policy
- `/contact` - Contact page

## Deployment

### Production Environment

1. **Environment Configuration**
   ```bash
   # Set production environment
   APP_ENV=prod
   APP_DEBUG=false
   
   # Use production database
   DATABASE_URL="mysql://user:password@localhost:3306/ecommerce_db"
   
   # Use live Razorpay keys
   RAZORPAY_KEY_ID=rzp_live_your_live_key
   RAZORPAY_KEY_SECRET=your_live_secret
   ```

2. **Optimize for Production**
   ```bash
   # Install production dependencies
   composer install --no-dev --optimize-autoloader
   
   # Clear and warm up cache
   php bin/console cache:clear --env=prod
   php bin/console cache:warmup --env=prod
   
   # Run database migrations
   php bin/console doctrine:migrations:migrate --env=prod
   ```

3. **Web Server Configuration**
   - Point document root to `public/` directory
   - Ensure `var/` directory is writable
   - Configure HTTPS for production

### Apache Configuration

```apache
<VirtualHost *:80>
    ServerName your-domain.com
    DocumentRoot /path/to/project/public
    
    <Directory /path/to/project/public>
        AllowOverride All
        Require all granted
    </Directory>
</VirtualHost>
```

### Nginx Configuration

```nginx
server {
    listen 80;
    server_name your-domain.com;
    root /path/to/project/public;
    
    location / {
        try_files $uri /index.php$is_args$args;
    }
    
    location ~ ^/index\.php(/|$) {
        fastcgi_pass unix:/var/run/php/php8.2-fpm.sock;
        fastcgi_split_path_info ^(.+\.php)(/.*)$;
        include fastcgi_params;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        fastcgi_param DOCUMENT_ROOT $realpath_root;
    }
}
```

## Customization

### Adding Products
- Use fixtures: Edit `src/DataFixtures/ProductFixtures.php`
- Admin interface: Create admin controllers (not included)
- Database: Insert directly into `products` table

### Styling
- Edit `templates/base.html.twig` for global styles
- Bootstrap 5 classes used throughout
- Custom CSS can be added to base template

### Payment Integration
- Razorpay configuration in `templates/checkout/index.html.twig`
- Success/failure handling in `CheckoutController`
- Extend for order management as needed

## Security Considerations

- Change `APP_SECRET` in production
- Use HTTPS for payment processing
- Validate Razorpay webhooks (not implemented)
- Implement proper user authentication if needed
- Regular security updates

## Support

For issues or questions:
- Check Symfony documentation: https://symfony.com/doc
- Razorpay documentation: https://razorpay.com/docs
- Create an issue in the project repository

## License

This project is open source. Modify as needed for your requirements.
