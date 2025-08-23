# Deployment Guide

This guide covers deploying your Symfony e-commerce application to production.

## Pre-Deployment Checklist

### 1. Environment Configuration
- [ ] Copy `.env.example` to `.env`
- [ ] Set `APP_ENV=prod`
- [ ] Set `APP_DEBUG=false`
- [ ] Generate secure `APP_SECRET`
- [ ] Configure production database URL
- [ ] Set live Razorpay keys
- [ ] Configure email settings (if needed)

### 2. Security
- [ ] Use HTTPS in production
- [ ] Secure database credentials
- [ ] Restrict file permissions
- [ ] Configure firewall rules
- [ ] Enable security headers

### 3. Performance
- [ ] Enable OPcache
- [ ] Configure caching
- [ ] Optimize autoloader
- [ ] Minify assets (if applicable)

## Deployment Steps

### Step 1: Server Preparation

```bash
# Update system packages
sudo apt update && sudo apt upgrade -y

# Install required packages
sudo apt install -y php8.2 php8.2-fpm php8.2-mysql php8.2-sqlite3 \
    php8.2-xml php8.2-mbstring php8.2-curl php8.2-zip \
    nginx mysql-server composer git

# Enable PHP extensions
sudo phpenmod pdo_mysql pdo_sqlite
```

### Step 2: Application Setup

```bash
# Clone or upload your application
cd /var/www
sudo git clone <your-repo> ecommerce
cd ecommerce

# Set proper ownership
sudo chown -R www-data:www-data /var/www/ecommerce
sudo chmod -R 755 /var/www/ecommerce

# Install dependencies
composer install --no-dev --optimize-autoloader

# Configure environment
cp .env.example .env
# Edit .env with production values
```

### Step 3: Database Setup

```bash
# For MySQL
mysql -u root -p
CREATE DATABASE ecommerce_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
CREATE USER 'ecommerce_user'@'localhost' IDENTIFIED BY 'secure_password';
GRANT ALL PRIVILEGES ON ecommerce_db.* TO 'ecommerce_user'@'localhost';
FLUSH PRIVILEGES;
EXIT;

# Run migrations
php bin/console doctrine:migrations:migrate --env=prod --no-interaction

# Load fixtures (optional for production)
php bin/console doctrine:fixtures:load --env=prod --no-interaction
```

### Step 4: Web Server Configuration

#### Nginx Configuration

Create `/etc/nginx/sites-available/ecommerce`:

```nginx
server {
    listen 80;
    server_name your-domain.com www.your-domain.com;
    root /var/www/ecommerce/public;
    index index.php;

    # Security headers
    add_header X-Frame-Options "SAMEORIGIN" always;
    add_header X-Content-Type-Options "nosniff" always;
    add_header X-XSS-Protection "1; mode=block" always;

    # Gzip compression
    gzip on;
    gzip_types text/plain text/css application/json application/javascript text/xml application/xml application/xml+rss text/javascript;

    location / {
        try_files $uri /index.php$is_args$args;
    }

    location ~ ^/index\.php(/|$) {
        fastcgi_pass unix:/var/run/php/php8.2-fpm.sock;
        fastcgi_split_path_info ^(.+\.php)(/.*)$;
        include fastcgi_params;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        fastcgi_param DOCUMENT_ROOT $realpath_root;
        internal;
    }

    location ~ \.php$ {
        return 404;
    }

    # Deny access to sensitive files
    location ~ /\. {
        deny all;
    }

    location ~ /(var|config|src|tests|vendor)/ {
        deny all;
    }

    # Cache static assets
    location ~* \.(jpg|jpeg|png|gif|ico|css|js|woff|woff2)$ {
        expires 1y;
        add_header Cache-Control "public, immutable";
    }
}
```

Enable the site:
```bash
sudo ln -s /etc/nginx/sites-available/ecommerce /etc/nginx/sites-enabled/
sudo nginx -t
sudo systemctl reload nginx
```

### Step 5: SSL Certificate (Let's Encrypt)

```bash
# Install Certbot
sudo apt install certbot python3-certbot-nginx

# Obtain SSL certificate
sudo certbot --nginx -d your-domain.com -d www.your-domain.com

# Test auto-renewal
sudo certbot renew --dry-run
```

### Step 6: Production Optimization

```bash
# Clear and warm up cache
php bin/console cache:clear --env=prod
php bin/console cache:warmup --env=prod

# Set proper permissions
sudo chown -R www-data:www-data var/
sudo chmod -R 775 var/

# Configure PHP-FPM for production
sudo nano /etc/php/8.2/fpm/php.ini
```

PHP-FPM optimizations:
```ini
; Production settings
opcache.enable=1
opcache.memory_consumption=256
opcache.max_accelerated_files=20000
opcache.validate_timestamps=0
opcache.save_comments=1
opcache.fast_shutdown=1

; Security
expose_php=Off
display_errors=Off
log_errors=On
```

### Step 7: Monitoring and Maintenance

#### Log Rotation
Create `/etc/logrotate.d/symfony`:
```
/var/www/ecommerce/var/log/*.log {
    daily
    missingok
    rotate 52
    compress
    delaycompress
    notifempty
    create 644 www-data www-data
}
```

#### Backup Script
Create `/usr/local/bin/backup-ecommerce.sh`:
```bash
#!/bin/bash
BACKUP_DIR="/var/backups/ecommerce"
DATE=$(date +%Y%m%d_%H%M%S)

# Create backup directory
mkdir -p $BACKUP_DIR

# Backup database
mysqldump -u ecommerce_user -p'secure_password' ecommerce_db > $BACKUP_DIR/db_$DATE.sql

# Backup application files
tar -czf $BACKUP_DIR/app_$DATE.tar.gz -C /var/www ecommerce

# Keep only last 7 days of backups
find $BACKUP_DIR -name "*.sql" -mtime +7 -delete
find $BACKUP_DIR -name "*.tar.gz" -mtime +7 -delete
```

Make executable and add to cron:
```bash
sudo chmod +x /usr/local/bin/backup-ecommerce.sh
sudo crontab -e
# Add: 0 2 * * * /usr/local/bin/backup-ecommerce.sh
```

## Post-Deployment Testing

### 1. Functional Testing
- [ ] Homepage loads correctly
- [ ] Product listing displays
- [ ] Product details work
- [ ] Checkout process functions
- [ ] Payment integration works (test mode first)
- [ ] Static pages accessible
- [ ] Mobile responsiveness

### 2. Performance Testing
- [ ] Page load times acceptable
- [ ] Database queries optimized
- [ ] Caching working properly
- [ ] SSL certificate valid

### 3. Security Testing
- [ ] HTTPS enforced
- [ ] Sensitive files not accessible
- [ ] Error pages don't expose information
- [ ] Payment data handled securely

## Troubleshooting

### Common Issues

**Permission Errors:**
```bash
sudo chown -R www-data:www-data /var/www/ecommerce
sudo chmod -R 755 /var/www/ecommerce
sudo chmod -R 775 /var/www/ecommerce/var
```

**Cache Issues:**
```bash
php bin/console cache:clear --env=prod
sudo chown -R www-data:www-data var/cache
```

**Database Connection:**
- Check database credentials in `.env`
- Verify database server is running
- Test connection manually

**Nginx 502 Error:**
- Check PHP-FPM status: `sudo systemctl status php8.2-fpm`
- Verify socket path in Nginx config
- Check PHP-FPM logs: `sudo tail -f /var/log/php8.2-fpm.log`

### Log Locations
- Nginx: `/var/log/nginx/`
- PHP-FPM: `/var/log/php8.2-fpm.log`
- Symfony: `/var/www/ecommerce/var/log/`
- System: `/var/log/syslog`

## Maintenance

### Regular Tasks
- Monitor disk space
- Check log files for errors
- Update dependencies: `composer update`
- Security updates: `sudo apt update && sudo apt upgrade`
- Backup verification
- SSL certificate renewal (automatic with Let's Encrypt)

### Scaling Considerations
- Database optimization and indexing
- CDN for static assets
- Load balancing for multiple servers
- Redis/Memcached for session storage
- Database replication for high availability

## Support

For deployment issues:
1. Check logs for specific error messages
2. Verify all configuration files
3. Test individual components
4. Consult Symfony documentation
5. Check server requirements and compatibility
