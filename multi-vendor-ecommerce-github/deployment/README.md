# Multi-Vendor E-commerce Platform

A complete multi-vendor e-commerce platform built with PHP, MySQL, HTML, CSS, and JavaScript. This platform allows multiple vendors to sell their products while providing a seamless shopping experience for customers.

## Features

### Customer Features
- **User Registration & Authentication**: Secure login/register system
- **Product Browsing**: Browse products by categories with search and filters
- **Product Details**: Detailed product pages with images, reviews, and ratings
- **Shopping Cart**: Add/remove items, update quantities
- **Wishlist**: Save favorite products for later
- **Checkout Process**: Secure checkout with multiple payment options
- **Order Tracking**: Track order status and history
- **Reviews & Ratings**: Rate and review purchased products

### Vendor Features
- **Vendor Dashboard**: Complete dashboard to manage business
- **Product Management**: Add, edit, delete products with multiple images
- **Inventory Management**: Track stock levels and manage inventory
- **Order Management**: View and manage customer orders
- **Sales Reports**: Detailed sales analytics and reports
- **Profile Management**: Update shop information and settings

### Admin Features
- **Admin Dashboard**: Comprehensive overview of the platform
- **Vendor Management**: Approve/reject vendor applications
- **Product Moderation**: Monitor and manage all products
- **Order Management**: Oversee all platform orders
- **Customer Management**: Manage customer accounts
- **Category Management**: Create and manage product categories
- **Reports & Analytics**: Platform-wide sales and performance reports
- **Settings Management**: Configure platform settings

### Technical Features
- **MVC Architecture**: Clean, organized code structure
- **Responsive Design**: Mobile-friendly Bootstrap-based UI
- **Payment Integration**: Razorpay, PayPal, and Cash on Delivery
- **Email Notifications**: Automated email system
- **Security**: Password hashing, CSRF protection, input sanitization
- **SEO Friendly**: Clean URLs and meta tags
- **File Upload**: Secure image upload system

## Technology Stack

- **Backend**: PHP 7.4+ with MVC pattern
- **Database**: MySQL 5.7+
- **Frontend**: HTML5, CSS3, JavaScript (jQuery)
- **Framework**: Bootstrap 5
- **Icons**: Font Awesome
- **Payment**: Razorpay, PayPal APIs

## Installation Guide

### Prerequisites
- PHP 7.4 or higher
- MySQL 5.7 or higher
- Apache/Nginx web server
- Composer (optional, for dependencies)

### Step 1: Download and Setup
1. Clone or download the project files
2. Extract to your web server directory (htdocs for XAMPP, www for WAMP)

### Step 2: Database Setup
1. Create a new MySQL database
2. Import the database schema:
   ```sql
   mysql -u username -p database_name < database/schema.sql
   ```
3. Update database credentials in `config/config.php`

### Step 3: Configuration
1. Open `config/config.php`
2. Update the following settings:
   ```php
   // Database Configuration
   define('DB_HOST', 'localhost');
   define('DB_NAME', 'your_database_name');
   define('DB_USER', 'your_username');
   define('DB_PASS', 'your_password');
   
   // Site Configuration
   define('SITE_URL', 'http://localhost/multi-vendor-ecommerce');
   define('SITE_NAME', 'Your Store Name');
   
   // Email Configuration
   define('SMTP_HOST', 'your_smtp_host');
   define('SMTP_USERNAME', 'your_email@domain.com');
   define('SMTP_PASSWORD', 'your_email_password');
   
   // Payment Gateway Keys
   define('RAZORPAY_KEY_ID', 'your_razorpay_key');
   define('RAZORPAY_KEY_SECRET', 'your_razorpay_secret');
   ```

### Step 4: File Permissions
Set proper permissions for upload directories:
```bash
chmod 755 assets/uploads/
chmod 755 assets/uploads/products/
chmod 755 assets/uploads/vendors/
chmod 755 assets/uploads/users/
```

### Step 5: Admin Account
The default admin account is created during database setup:
- **Email**: admin@example.com
- **Password**: admin123

**Important**: Change the admin password immediately after first login.

## Directory Structure

```
multi-vendor-ecommerce/
├── api/                    # API endpoints
│   ├── cart.php
│   ├── cart-count.php
│   ├── checkout.php
│   ├── reviews.php
│   ├── verify-payment.php
│   └── wishlist.php
├── assets/                 # Static assets
│   ├── css/
│   ├── js/
│   ├── images/
│   └── uploads/
├── config/                 # Configuration files
│   ├── config.php
│   └── database.php
├── controllers/            # MVC Controllers
│   ├── AdminController.php
│   ├── AuthController.php
│   ├── CartController.php
│   ├── HomeController.php
│   ├── ProductController.php
│   └── VendorController.php
├── database/              # Database files
│   └── schema.sql
├── includes/              # Helper functions
│   └── functions.php
├── models/                # MVC Models
│   ├── BaseModel.php
│   ├── Product.php
│   └── User.php
├── views/                 # MVC Views
│   ├── admin/
│   ├── auth/
│   ├── cart/
│   ├── layout/
│   ├── products/
│   └── vendor/
├── index.php              # Main entry point
└── README.md
```

## Usage Guide

### For Customers
1. **Registration**: Create an account or login
2. **Browse Products**: Use categories, search, and filters
3. **Add to Cart**: Select products and add to cart
4. **Checkout**: Provide shipping details and choose payment method
5. **Track Orders**: Monitor order status in your account

### For Vendors
1. **Apply**: Register as a vendor and wait for approval
2. **Setup Shop**: Complete your vendor profile
3. **Add Products**: Upload products with images and details
4. **Manage Orders**: Process customer orders
5. **View Reports**: Monitor sales and performance

### For Admins
1. **Login**: Use admin credentials to access admin panel
2. **Approve Vendors**: Review and approve vendor applications
3. **Manage Content**: Oversee products, orders, and users
4. **Configure Settings**: Update platform settings
5. **View Reports**: Monitor platform performance

## Payment Gateway Setup

### Razorpay Integration
1. Create a Razorpay account at https://razorpay.com
2. Get your API keys from the dashboard
3. Update the keys in `config/config.php`
4. Test with Razorpay test mode first

### PayPal Integration
1. Create a PayPal developer account
2. Create a new application
3. Get your client ID and secret
4. Update the credentials in configuration

## Security Features

- **Password Hashing**: All passwords are hashed using PHP's password_hash()
- **SQL Injection Protection**: All database queries use prepared statements
- **XSS Protection**: All user inputs are sanitized
- **CSRF Protection**: Forms include CSRF tokens
- **File Upload Security**: File types and sizes are validated
- **Session Security**: Secure session handling

## Customization

### Themes
- Modify CSS files in `assets/css/`
- Update Bootstrap variables for color schemes
- Customize layouts in `views/layout/`

### Adding Features
- Create new controllers in `controllers/`
- Add corresponding models in `models/`
- Create views in appropriate `views/` subdirectories
- Update routing in `index.php`

## Troubleshooting

### Common Issues

1. **Database Connection Error**
   - Check database credentials in `config/config.php`
   - Ensure MySQL service is running
   - Verify database exists

2. **File Upload Issues**
   - Check directory permissions
   - Verify PHP upload settings in php.ini
   - Ensure upload directories exist

3. **Email Not Sending**
   - Verify SMTP settings
   - Check firewall settings
   - Test with a different email provider

4. **Payment Gateway Issues**
   - Verify API keys are correct
   - Check if using test/live mode appropriately
   - Review payment gateway documentation

## Performance Optimization

- Enable PHP OPcache
- Use MySQL query optimization
- Implement image compression
- Enable GZIP compression
- Use CDN for static assets
- Implement caching mechanisms

## Support

For support and questions:
- Check the documentation
- Review common issues in troubleshooting
- Contact the development team

## License

This project is licensed under the MIT License. See LICENSE file for details.

## Contributing

1. Fork the repository
2. Create a feature branch
3. Make your changes
4. Test thoroughly
5. Submit a pull request

## Changelog

### Version 1.0.0
- Initial release
- Complete multi-vendor functionality
- Payment gateway integration
- Admin panel
- Vendor dashboard
- Customer features

---

**Note**: This is a complete e-commerce solution. Make sure to test all features thoroughly before deploying to production. Always keep backups of your database and files.
