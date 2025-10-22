# CHaRT Modern Deployment Guide

## Linode Server Setup

### 1. Create Linode Instance

**Recommended Configuration:**
- **Plan**: Nanode 1GB (for development) or Linode 2GB (for production)
- **OS**: Ubuntu 22.04 LTS
- **Region**: Choose closest to your users
- **Root Password**: Set a strong password

### 2. Initial Server Setup

```bash
# Update system
sudo apt update && sudo apt upgrade -y

# Install essential packages
sudo apt install -y curl wget git unzip software-properties-common

# Create deployment user
sudo adduser chart
sudo usermod -aG sudo chart
sudo usermod -aG www-data chart
```

### 3. Install LAMP Stack

```bash
# Install Apache
sudo apt install -y apache2
sudo systemctl enable apache2
sudo systemctl start apache2

# Install MySQL
sudo apt install -y mysql-server
sudo mysql_secure_installation

# Install PHP 8.2
sudo add-apt-repository ppa:ondrej/php -y
sudo apt update
sudo apt install -y php8.2 php8.2-cli php8.2-common php8.2-mysql php8.2-zip php8.2-gd php8.2-mbstring php8.2-curl php8.2-xml php8.2-bcmath php82-intl php8.2-xmlrpc php8.2-soap php8.2-readline php8.2-opcache php8.2-ldap php8.2-imagick php8.2-dev php8.2-redis php8.2-memcached php8.2-xdebug

# Install Composer
curl -sS https://getcomposer.org/installer | php
sudo mv composer.phar /usr/local/bin/composer
sudo chmod +x /usr/local/bin/composer

# Install Node.js (for Vite)
curl -fsSL https://deb.nodesource.com/setup_20.x | sudo -E bash -
sudo apt install -y nodejs
```

### 4. Configure Apache

```bash
# Enable required modules
sudo a2enmod rewrite
sudo a2enmod ssl
sudo a2enmod headers

# Create virtual host
sudo nano /etc/apache2/sites-available/chart.conf
```

**Virtual Host Configuration:**
```apache
<VirtualHost *:80>
    ServerName chart.yourdomain.com
    DocumentRoot /var/www/chart/public
    
    <Directory /var/www/chart/public>
        AllowOverride All
        Require all granted
    </Directory>
    
    ErrorLog ${APACHE_LOG_DIR}/chart_error.log
    CustomLog ${APACHE_LOG_DIR}/chart_access.log combined
</VirtualHost>
```

### 5. Database Setup

```bash
# Create database and user
sudo mysql -u root -p

# In MySQL:
CREATE DATABASE chart_production;
CREATE USER 'chart_user'@'localhost' IDENTIFIED BY 'strong_password_here';
GRANT ALL PRIVILEGES ON chart_production.* TO 'chart_user'@'localhost';
FLUSH PRIVILEGES;
EXIT;
```

### 6. Deploy Application

```bash
# Clone repository
cd /var/www
sudo git clone https://github.com/yourusername/CHART.git chart
sudo chown -R chart:www-data /var/www/chart
cd /var/www/chart

# Install dependencies
composer install --optimize-autoloader --no-dev
npm install
npm run build

# Set up environment
cp .env.example .env
nano .env
```

### 7. Environment Configuration

**Production .env file:**
```env
APP_NAME=CHaRT
APP_ENV=production
APP_KEY=base64:your-generated-key-here
APP_DEBUG=false
APP_URL=https://chart.yourdomain.com

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=chart_production
DB_USERNAME=chart_user
DB_PASSWORD=strong_password_here

MAIL_MAILER=smtp
MAIL_HOST=your-smtp-server
MAIL_PORT=587
MAIL_USERNAME=your-email
MAIL_PASSWORD=your-password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=noreply@yourdomain.com
MAIL_FROM_NAME="${APP_NAME}"
```

### 8. Run Migrations and Seeders

```bash
# Generate application key
php artisan key:generate

# Run migrations
php artisan migrate --force

# Seed database
php artisan db:seed --force

# Cache configuration
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

### 9. SSL/HTTPS Setup

```bash
# Install Certbot
sudo apt install -y certbot python3-certbot-apache

# Get SSL certificate
sudo certbot --apache -d chart.yourdomain.com

# Auto-renewal
sudo crontab -e
# Add: 0 12 * * * /usr/bin/certbot renew --quiet
```

### 10. Security Hardening

```bash
# Configure firewall
sudo ufw enable
sudo ufw allow ssh
sudo ufw allow 'Apache Full'

# Set proper permissions
sudo chown -R chart:www-data /var/www/chart
sudo chmod -R 755 /var/www/chart
sudo chmod -R 775 /var/www/chart/storage
sudo chmod -R 775 /var/www/chart/bootstrap/cache
```

## Deployment Scripts

### Quick Deploy Script

```bash
#!/bin/bash
# deploy.sh

echo "Deploying CHaRT application..."

# Pull latest changes
cd /var/www/chart
git pull origin main

# Install/update dependencies
composer install --optimize-autoloader --no-dev
npm install
npm run build

# Run migrations
php artisan migrate --force

# Clear and cache
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Restart services
sudo systemctl restart apache2

echo "Deployment complete!"
```

### Backup Script

```bash
#!/bin/bash
# backup.sh

DATE=$(date +%Y%m%d_%H%M%S)
BACKUP_DIR="/var/backups/chart"

# Create backup directory
mkdir -p $BACKUP_DIR

# Database backup
mysqldump -u chart_user -p chart_production > $BACKUP_DIR/database_$DATE.sql

# Application backup
tar -czf $BACKUP_DIR/application_$DATE.tar.gz /var/www/chart

# Clean old backups (keep 7 days)
find $BACKUP_DIR -name "*.sql" -mtime +7 -delete
find $BACKUP_DIR -name "*.tar.gz" -mtime +7 -delete

echo "Backup completed: $DATE"
```

## Monitoring and Maintenance

### 1. Log Monitoring

```bash
# Application logs
tail -f /var/www/chart/storage/logs/laravel.log

# Apache logs
tail -f /var/log/apache2/chart_error.log
tail -f /var/log/apache2/chart_access.log

# System logs
tail -f /var/log/syslog
```

### 2. Performance Monitoring

```bash
# Install htop for system monitoring
sudo apt install -y htop

# Monitor MySQL
sudo mysql -u root -p
SHOW PROCESSLIST;
```

### 3. Regular Maintenance

```bash
# Weekly maintenance script
#!/bin/bash
# maintenance.sh

# Clear Laravel caches
cd /var/www/chart
php artisan cache:clear
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Update system
sudo apt update && sudo apt upgrade -y

# Restart services
sudo systemctl restart apache2
sudo systemctl restart mysql
```

## Troubleshooting

### Common Issues

1. **Permission Errors**
   ```bash
   sudo chown -R chart:www-data /var/www/chart
   sudo chmod -R 755 /var/www/chart
   sudo chmod -R 775 /var/www/chart/storage
   ```

2. **Database Connection Issues**
   - Check MySQL service: `sudo systemctl status mysql`
   - Verify credentials in `.env`
   - Test connection: `php artisan tinker`

3. **SSL Certificate Issues**
   - Check certificate: `sudo certbot certificates`
   - Renew certificate: `sudo certbot renew`

4. **Performance Issues**
   - Enable OPcache in PHP
   - Configure MySQL for better performance
   - Use Redis for caching (optional)

## Security Checklist

- [ ] Firewall configured
- [ ] SSL certificate installed
- [ ] Database secured
- [ ] File permissions set
- [ ] Regular backups scheduled
- [ ] Monitoring in place
- [ ] Updates automated

## Next Steps

1. **Domain Setup**: Point your domain to the Linode IP
2. **DNS Configuration**: Set up A records
3. **Email Configuration**: Configure SMTP for notifications
4. **Monitoring**: Set up application monitoring
5. **Backup Strategy**: Implement automated backups
