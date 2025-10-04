# Quick Start Guide - Multi-Vendor E-commerce Platform

## 🚀 Getting Started in 5 Minutes

### Step 1: Install XAMPP
1. Download XAMPP from: https://www.apachefriends.org/download.html
2. Install to `C:\xampp`
3. Start Apache and MySQL from XAMPP Control Panel

### Step 2: Setup Project
1. Copy the entire `multi-vendor-ecommerce` folder to `C:\xampp\htdocs\`
2. The path should be: `C:\xampp\htdocs\multi-vendor-ecommerce\`

### Step 3: Create Database
1. Open browser and go to: http://localhost/phpmyadmin
2. Create a new database named: `ecommerce_db`
3. Import the database schema:
   - Click on `ecommerce_db` database
   - Go to "Import" tab
   - Choose file: `database/schema.sql`
   - Click "Go" to import

### Step 4: Configure Database Connection
Edit `config/config.php` and update these lines:
```php
define('DB_HOST', 'localhost');
define('DB_NAME', 'ecommerce_db');
define('DB_USER', 'root');
define('DB_PASS', ''); // Leave empty for XAMPP default
```

### Step 5: Set Folder Permissions
Create these upload folders if they don't exist:
- `assets/uploads/products/`
- `assets/uploads/vendors/`
- `assets/uploads/users/`

### Step 6: Access the Application
Open your browser and go to: http://localhost/multi-vendor-ecommerce

## 🔑 Default Login Credentials

### Admin Login
- URL: http://localhost/multi-vendor-ecommerce?page=auth&action=login
- Email: admin@example.com
- Password: admin123

### Test Customer
- Email: customer@example.com
- Password: customer123

### Test Vendor
- Email: vendor@example.com
- Password: vendor123

## 🛠 Troubleshooting

### Common Issues:

1. **"Database connection failed"**
   - Make sure MySQL is running in XAMPP
   - Check database credentials in `config/config.php`
   - Ensure database `ecommerce_db` exists

2. **"Page not found" errors**
   - Make sure Apache is running
   - Check if mod_rewrite is enabled
   - Verify the project is in `htdocs` folder

3. **Image upload issues**
   - Check if upload folders exist and have write permissions
   - Verify PHP upload settings in `php.ini`

4. **Email not working**
   - Update SMTP settings in `config/config.php`
   - For testing, you can disable email notifications

## 📱 Testing the Platform

### As a Customer:
1. Register a new account or use test credentials
2. Browse products and categories
3. Add items to cart and wishlist
4. Complete checkout process
5. Leave reviews for products

### As a Vendor:
1. Login with vendor credentials
2. Add new products with images
3. Manage inventory and orders
4. View sales reports

### As an Admin:
1. Login with admin credentials
2. Approve/reject vendor applications
3. Manage products and categories
4. View platform analytics

## 🎯 Next Steps

1. **Customize Design**: Modify CSS files in `assets/css/`
2. **Add Payment Gateways**: Configure Razorpay/PayPal keys
3. **Setup Email**: Configure SMTP for notifications
4. **Add Content**: Upload product images and descriptions
5. **Go Live**: Follow deployment guide for production

## 📞 Need Help?

- Check the main README.md for detailed documentation
- Review DEPLOYMENT.md for production setup
- All configuration options are in `config/config.php`

---
**Happy Selling! 🛒**
