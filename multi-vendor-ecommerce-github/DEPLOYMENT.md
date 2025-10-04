# Deployment Guide - Multi-Vendor E-commerce Platform

This guide covers deploying the multi-vendor e-commerce platform to various hosting environments.

## Pre-Deployment Checklist

### 1. Code Preparation
- [ ] All configuration files updated with production values
- [ ] Database credentials configured
- [ ] Payment gateway keys added
- [ ] Email settings configured
- [ ] Debug mode disabled
- [ ] Error reporting set to production level
- [ ] All file permissions set correctly

### 2. Security Checklist
- [ ] Default admin password changed
- [ ] Strong database passwords used
- [ ] HTTPS enabled
- [ ] Sensitive files protected
- [ ] File upload restrictions in place
- [ ] SQL injection protection verified
- [ ] XSS protection implemented

### 3. Performance Checklist
- [ ] Images optimized
- [ ] CSS/JS minified
- [ ] Database indexes created
- [ ] Caching enabled
- [ ] GZIP compression enabled

## Deployment Options

## Option 1: Shared Hosting (cPanel)

### Requirements
- PHP 7.4+
- MySQL 5.7+
- At least 1GB storage
- SSL certificate

### Steps

1. **Prepare Files**
   ```bash
   # Create deployment package
   zip -r ecommerce-deploy.zip . -x "*.git*" "*.DS_Store*" "node_modules/*"
   ```

2. **Upload Files**
   - Login to cPanel File Manager
   - Navigate to public_html directory
   - Upload and extract the zip file
   - Set file permissions (755 for directories, 644 for files)

3. **Database Setup**
   - Create MySQL database via cPanel
   - Create database user and assign privileges
   - Import schema.sql via phpMyAdmin
   - Update database credentials in config/config.php

4. **Configuration**
   ```php
   // config/config.php - Production settings
   define('DB_HOST', 'localhost');
   define('DB_NAME', 'your_db_name');
   define('DB_USER', 'your_db_user');
   define('DB_PASS', 'your_db_password');
   
   define('SITE_URL', 'https://yourdomain.com');
   define('DEBUG_MODE', false);
   
   // Email settings
   define('SMTP_HOST', 'mail.yourdomain.com');
   define('SMTP_PORT', 587);
   define('SMTP_USERNAME', 'noreply@yourdomain.com');
   define('SMTP_PASSWORD', 'your_email_password');
   ```

5. **SSL Setup**
   - Enable SSL certificate in cPanel
   - Update SITE_URL to use https://
   - Add redirect rules in .htaccess

6. **Final Steps**
   - Test all functionality
   - Change default admin password
   - Configure payment gateways
   - Set up email notifications

## Option 2: VPS/Dedicated Server (Ubuntu/CentOS)

### Requirements
- Ubuntu 18.04+ or CentOS 7+
- Root or sudo access
- Domain name pointing to server

### Installation Steps

1. **Server Setup**
   ```bash
   # Update system
   sudo apt update && sudo apt upgrade -y
   
   # Install LAMP stack
   sudo apt install apache2 mysql-server php7.4 php7.4-mysql php7.4-curl php7.4-gd php7.4-mbstring php7.4-xml php7.4-zip -y
   
   # Enable Apache modules
   sudo a2enmod rewrite
   sudo systemctl restart apache2
   ```

2. **MySQL Configuration**
   ```bash
   # Secure MySQL installation
   sudo mysql_secure_installation
   
   # Create database
   sudo mysql -u root -p
   CREATE DATABASE ecommerce_db;
   CREATE USER 'ecommerce_user'@'localhost' IDENTIFIED BY 'strong_password';
   GRANT ALL PRIVILEGES ON ecommerce_db.* TO 'ecommerce_user'@'localhost';
   FLUSH PRIVILEGES;
   EXIT;
   ```

3. **Deploy Application**
   ```bash
   # Clone or upload files
   cd /var/www/html
   sudo git clone your-repository.git ecommerce
   
   # Set permissions
   sudo chown -R www-data:www-data /var/www/html/ecommerce
   sudo chmod -R 755 /var/www/html/ecommerce
   sudo chmod -R 777 /var/www/html/ecommerce/assets/uploads
   ```

4. **Apache Virtual Host**
   ```bash
   # Create virtual host
   sudo nano /etc/apache2/sites-available/ecommerce.conf
   ```
   
   ```apache
   <VirtualHost *:80>
       ServerName yourdomain.com
       ServerAlias www.yourdomain.com
       DocumentRoot /var/www/html/ecommerce
       
       <Directory /var/www/html/ecommerce>
           AllowOverride All
           Require all granted
       </Directory>
       
       ErrorLog ${APACHE_LOG_DIR}/ecommerce_error.log
       CustomLog ${APACHE_LOG_DIR}/ecommerce_access.log combined
   </VirtualHost>
   ```
   
   ```bash
   # Enable site
   sudo a2ensite ecommerce.conf
   sudo systemctl reload apache2
   ```

5. **SSL Certificate (Let's Encrypt)**
   ```bash
   # Install Certbot
   sudo apt install certbot python3-certbot-apache -y
   
   # Get SSL certificate
   sudo certbot --apache -d yourdomain.com -d www.yourdomain.com
   ```

6. **Import Database**
   ```bash
   mysql -u ecommerce_user -p ecommerce_db < database/schema.sql
   ```

## Option 3: Docker Deployment

### Docker Compose Setup

1. **Create docker-compose.yml**
   ```yaml
   version: '3.8'
   
   services:
     web:
       build: .
       ports:
         - "80:80"
         - "443:443"
       volumes:
         - ./:/var/www/html
         - ./docker/apache/sites-available:/etc/apache2/sites-available
       depends_on:
         - db
       environment:
         - DB_HOST=db
         - DB_NAME=ecommerce
         - DB_USER=root
         - DB_PASS=rootpassword
   
     db:
       image: mysql:8.0
       environment:
         MYSQL_ROOT_PASSWORD: rootpassword
         MYSQL_DATABASE: ecommerce
       volumes:
         - mysql_data:/var/lib/mysql
         - ./database/schema.sql:/docker-entrypoint-initdb.d/schema.sql
       ports:
         - "3306:3306"
   
     phpmyadmin:
       image: phpmyadmin/phpmyadmin
       environment:
         PMA_HOST: db
         PMA_USER: root
         PMA_PASSWORD: rootpassword
       ports:
         - "8080:80"
       depends_on:
         - db
   
   volumes:
     mysql_data:
   ```

2. **Create Dockerfile**
   ```dockerfile
   FROM php:7.4-apache
   
   # Install PHP extensions
   RUN docker-php-ext-install mysqli pdo pdo_mysql gd
   
   # Enable Apache modules
   RUN a2enmod rewrite
   
   # Copy application files
   COPY . /var/www/html/
   
   # Set permissions
   RUN chown -R www-data:www-data /var/www/html
   RUN chmod -R 755 /var/www/html
   RUN chmod -R 777 /var/www/html/assets/uploads
   
   EXPOSE 80
   ```

3. **Deploy**
   ```bash
   # Build and start containers
   docker-compose up -d
   
   # Check status
   docker-compose ps
   ```

## Option 4: Cloud Deployment (AWS/DigitalOcean)

### AWS EC2 Deployment

1. **Launch EC2 Instance**
   - Choose Ubuntu 20.04 LTS AMI
   - Select appropriate instance type (t3.micro for testing)
   - Configure security groups (HTTP, HTTPS, SSH)
   - Launch with key pair

2. **Connect and Setup**
   ```bash
   # Connect to instance
   ssh -i your-key.pem ubuntu@your-ec2-ip
   
   # Follow VPS deployment steps above
   ```

3. **RDS Database (Optional)**
   - Create RDS MySQL instance
   - Update database configuration
   - Import schema via MySQL client

### DigitalOcean Droplet

1. **Create Droplet**
   - Choose Ubuntu 20.04
   - Select appropriate size
   - Add SSH key
   - Create droplet

2. **Setup Application**
   ```bash
   # Follow VPS deployment steps
   # Configure domain DNS to point to droplet IP
   ```

## Post-Deployment Configuration

### 1. Payment Gateway Setup

**Razorpay**
```php
// config/config.php
define('RAZORPAY_KEY_ID', 'rzp_live_your_key_id');
define('RAZORPAY_KEY_SECRET', 'your_secret_key');
```

**PayPal**
```php
// config/config.php
define('PAYPAL_CLIENT_ID', 'your_paypal_client_id');
define('PAYPAL_CLIENT_SECRET', 'your_paypal_secret');
define('PAYPAL_MODE', 'live'); // or 'sandbox' for testing
```

### 2. Email Configuration

**SMTP Setup**
```php
// config/config.php
define('SMTP_HOST', 'smtp.gmail.com');
define('SMTP_PORT', 587);
define('SMTP_USERNAME', 'your-email@gmail.com');
define('SMTP_PASSWORD', 'your-app-password');
define('SMTP_ENCRYPTION', 'tls');
```

### 3. Cron Jobs Setup

```bash
# Add to crontab
crontab -e

# Add these lines
# Send daily sales reports
0 9 * * * /usr/bin/php /var/www/html/ecommerce/cron/daily-reports.php

# Clean up expired sessions
0 2 * * * /usr/bin/php /var/www/html/ecommerce/cron/cleanup.php

# Update vendor commissions
0 1 * * * /usr/bin/php /var/www/html/ecommerce/cron/update-commissions.php
```

### 4. Backup Setup

```bash
# Create backup script
nano /home/backup-ecommerce.sh
```

```bash
#!/bin/bash
DATE=$(date +%Y%m%d_%H%M%S)
BACKUP_DIR="/home/backups"
DB_NAME="ecommerce_db"
DB_USER="ecommerce_user"
DB_PASS="your_password"

# Create backup directory
mkdir -p $BACKUP_DIR

# Database backup
mysqldump -u $DB_USER -p$DB_PASS $DB_NAME > $BACKUP_DIR/db_backup_$DATE.sql

# Files backup
tar -czf $BACKUP_DIR/files_backup_$DATE.tar.gz /var/www/html/ecommerce

# Remove old backups (keep last 7 days)
find $BACKUP_DIR -name "*.sql" -mtime +7 -delete
find $BACKUP_DIR -name "*.tar.gz" -mtime +7 -delete
```

```bash
# Make executable and add to cron
chmod +x /home/backup-ecommerce.sh
crontab -e
# Add: 0 3 * * * /home/backup-ecommerce.sh
```

## Monitoring and Maintenance

### 1. Log Monitoring
```bash
# Apache logs
tail -f /var/log/apache2/ecommerce_error.log
tail -f /var/log/apache2/ecommerce_access.log

# MySQL logs
tail -f /var/log/mysql/error.log
```

### 2. Performance Monitoring
- Set up monitoring tools (New Relic, DataDog)
- Monitor server resources
- Track application performance
- Set up alerts for critical issues

### 3. Security Updates
```bash
# Regular system updates
sudo apt update && sudo apt upgrade -y

# Update application dependencies
# Monitor security advisories
# Apply security patches promptly
```

## Troubleshooting Common Issues

### 1. File Permission Issues
```bash
# Fix ownership
sudo chown -R www-data:www-data /var/www/html/ecommerce

# Fix permissions
find /var/www/html/ecommerce -type d -exec chmod 755 {} \;
find /var/www/html/ecommerce -type f -exec chmod 644 {} \;
chmod -R 777 /var/www/html/ecommerce/assets/uploads
```

### 2. Database Connection Issues
- Verify database credentials
- Check MySQL service status
- Review firewall settings
- Test connection manually

### 3. Email Issues
- Verify SMTP settings
- Check firewall for SMTP ports
- Test with different email providers
- Review email logs

### 4. Payment Gateway Issues
- Verify API keys
- Check webhook URLs
- Review payment gateway logs
- Test in sandbox mode first

## Scaling Considerations

### 1. Database Optimization
- Add proper indexes
- Optimize queries
- Consider read replicas
- Implement connection pooling

### 2. File Storage
- Use CDN for static assets
- Implement image optimization
- Consider cloud storage (S3, CloudFlare)

### 3. Caching
- Implement Redis/Memcached
- Use application-level caching
- Enable browser caching
- Use reverse proxy (Nginx)

### 4. Load Balancing
- Multiple application servers
- Database load balancing
- CDN implementation
- Auto-scaling groups

---

This deployment guide covers the most common deployment scenarios. Choose the option that best fits your requirements and infrastructure. Always test thoroughly in a staging environment before deploying to production.
