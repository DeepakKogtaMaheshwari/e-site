# 🔧 B2Battle Troubleshooting Guide

## 🚨 **500 Internal Server Error on Products Page**

### **Quick Fix Steps:**

#### **1. Database Setup (Most Common Issue):**
```bash
# Run the database setup script
php setup_database.php

# OR manually run these commands:
php bin/console doctrine:database:create --if-not-exists
php bin/console doctrine:migrations:migrate --no-interaction
php bin/console doctrine:fixtures:load --no-interaction
php bin/console cache:clear
```

#### **2. Check Database Connection:**
- Verify your `.env` file has correct database settings:
```env
DATABASE_URL="mysql://username:password@127.0.0.1:3306/b2battle?serverVersion=8.0&charset=utf8mb4"
```

#### **3. XAMPP/WAMP Issues:**
- Make sure MySQL is running in XAMPP/WAMP
- Check if database `b2battle` exists in phpMyAdmin
- Verify PHP version is 8.1 or higher

#### **4. Permission Issues:**
```bash
# Set proper permissions (Linux/Mac)
chmod -R 775 var/
chmod -R 775 public/

# Windows: Make sure var/ folder is writable
```

#### **5. Clear All Caches:**
```bash
php bin/console cache:clear
php bin/console cache:clear --env=prod
```

---

## 🐛 **Common Issues & Solutions:**

### **Issue: "Class not found" errors**
**Solution:**
```bash
composer dump-autoload
composer install
```

### **Issue: "Table doesn't exist" errors**
**Solution:**
```bash
php bin/console doctrine:schema:update --force
php bin/console doctrine:fixtures:load --no-interaction
```

### **Issue: "Access denied" database errors**
**Solution:**
- Check database credentials in `.env`
- Make sure MySQL user has proper permissions
- Try connecting with phpMyAdmin first

### **Issue: Products page shows empty**
**Solution:**
- The repository now has fallback sample products
- Run: `php bin/console doctrine:fixtures:load --no-interaction`
- Check if products table has data in phpMyAdmin

### **Issue: CSS/JS not loading**
**Solution:**
- Check if `public/` directory is web root
- Verify `.htaccess` file exists in `public/`
- Clear browser cache

---

## 📋 **Development Environment Check:**

### **Required:**
- ✅ PHP 8.1 or higher
- ✅ MySQL 5.7+ or MariaDB 10.3+
- ✅ Composer installed
- ✅ Web server (Apache/Nginx) or Symfony CLI

### **Verify Setup:**
```bash
# Check PHP version
php -v

# Check Composer
composer --version

# Check Symfony requirements
symfony check:requirements

# Test database connection
php bin/console doctrine:database:create --if-not-exists
```

---

## 🚀 **Production Deployment Issues:**

### **Issue: 500 error on live server**
**Solutions:**
1. Check server error logs
2. Verify PHP version on server
3. Set proper file permissions
4. Configure `.env.local` with production settings
5. Run deployment script: `./deploy.sh`

### **Issue: Database connection on live server**
**Solutions:**
1. Update database credentials in `.env.local`
2. Check if database exists on server
3. Verify database user permissions
4. Test connection with hosting provider tools

### **Issue: Payment gateway not working**
**Solutions:**
1. Update Razorpay credentials to LIVE keys
2. Verify webhook URLs
3. Check SSL certificate is installed
4. Test with small amounts first

---

## 📞 **Getting Help:**

### **Check Logs:**
- **Symfony logs:** `var/log/dev.log` or `var/log/prod.log`
- **Web server logs:** Check Apache/Nginx error logs
- **PHP logs:** Check PHP error log

### **Debug Mode:**
```bash
# Enable debug mode
export APP_ENV=dev
export APP_DEBUG=1
php bin/console cache:clear
```

### **Test URLs:**
- **Homepage:** http://localhost:8001/
- **Products:** http://localhost:8001/products
- **Contact:** http://localhost:8001/contact
- **Test Cart:** http://localhost:8001/test-cart

---

## ✅ **Quick Health Check:**

Run this command to verify everything is working:
```bash
php bin/console about
```

If you see green checkmarks, your environment is ready!

---

## 🎯 **Still Having Issues?**

1. **Check the exact error message** in browser developer tools
2. **Look at Symfony logs** in `var/log/`
3. **Verify database connection** in phpMyAdmin
4. **Test with sample data** using the repository fallback
5. **Contact support** with specific error messages

**Remember:** The site now has fallback sample products, so it should work even without a database!
