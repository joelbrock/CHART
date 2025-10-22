# CHaRT Modern - Linode Deployment Guide

## 🚀 Quick Start Deployment

### Option 1: Traditional LAMP Deployment

1. **Create Linode Instance**
   - Plan: Linode 2GB (recommended for production)
   - OS: Ubuntu 22.04 LTS
   - Region: Choose closest to your users

2. **Run Deployment Script**
   ```bash
   # On your local machine, upload the application
   scp -r modern-chart/ root@your-linode-ip:/var/www/
   
   # SSH into your Linode
   ssh root@your-linode-ip
   
   # Navigate to application directory
   cd /var/www/modern-chart
   
   # Make scripts executable
   chmod +x deploy.sh backup.sh maintenance.sh
   
   # Run deployment script
   ./deploy.sh
   ```

3. **Configure Database**
   ```bash
   # Secure MySQL installation
   sudo mysql_secure_installation
   
   # Create database and user
   sudo mysql -u root -p
   ```
   
   In MySQL:
   ```sql
   CREATE DATABASE chart_production;
   CREATE USER 'chart_user'@'localhost' IDENTIFIED BY 'your_secure_password';
   GRANT ALL PRIVILEGES ON chart_production.* TO 'chart_user'@'localhost';
   FLUSH PRIVILEGES;
   EXIT;
   ```

4. **Configure Application**
   ```bash
   # Copy production environment
   cp production.env .env
   nano .env  # Update with your settings
   
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

### Option 2: Docker Deployment

1. **Install Docker on Linode**
   ```bash
   # Update system
   sudo apt update && sudo apt upgrade -y
   
   # Install Docker
   curl -fsSL https://get.docker.com -o get-docker.sh
   sudo sh get-docker.sh
   sudo usermod -aG docker $USER
   
   # Install Docker Compose
   sudo curl -L "https://github.com/docker/compose/releases/latest/download/docker-compose-$(uname -s)-$(uname -m)" -o /usr/local/bin/docker-compose
   sudo chmod +x /usr/local/bin/docker-compose
   ```

2. **Deploy with Docker**
   ```bash
   # Clone or upload application
   git clone https://github.com/yourusername/CHART.git
   cd CHART/modern-chart
   
   # Configure environment
   cp production.env .env
   nano .env  # Update database credentials
   
   # Start services
   docker-compose up -d
   
   # Check status
   docker-compose ps
   ```

## 🔧 Configuration

### Environment Variables

Update your `.env` file with production values:

```env
APP_NAME=CHaRT
APP_ENV=production
APP_DEBUG=false
APP_URL=https://chart.yourdomain.com

DB_CONNECTION=mysql
DB_HOST=127.0.0.1  # or 'db' for Docker
DB_PORT=3306
DB_DATABASE=chart_production
DB_USERNAME=chart_user
DB_PASSWORD=your_secure_password

MAIL_MAILER=smtp
MAIL_HOST=your-smtp-server.com
MAIL_PORT=587
MAIL_USERNAME=your-email@yourdomain.com
MAIL_PASSWORD=your-email-password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=noreply@yourdomain.com
```

### SSL/HTTPS Setup

1. **Install Certbot**
   ```bash
   sudo apt install -y certbot python3-certbot-apache
   ```

2. **Get SSL Certificate**
   ```bash
   sudo certbot --apache -d chart.yourdomain.com
   ```

3. **Auto-renewal**
   ```bash
   sudo crontab -e
   # Add: 0 12 * * * /usr/bin/certbot renew --quiet
   ```

## 📊 Monitoring & Maintenance

### Automated Backups

1. **Set up daily backups**
   ```bash
   # Add to crontab
   sudo crontab -e
   
   # Add this line for daily backups at 2 AM
   0 2 * * * /var/www/chart/backup.sh
   ```

2. **Weekly maintenance**
   ```bash
   # Add to crontab
   0 3 * * 0 /var/www/chart/maintenance.sh
   ```

### Monitoring

1. **Install monitoring tools**
   ```bash
   # Install htop for system monitoring
   sudo apt install -y htop
   
   # Install fail2ban for security
   sudo apt install -y fail2ban
   sudo systemctl enable fail2ban
   sudo systemctl start fail2ban
   ```

2. **Set up log monitoring**
   ```bash
   # Application logs
   tail -f /var/www/chart/storage/logs/laravel.log
   
   # Apache logs
   tail -f /var/log/apache2/chart_error.log
   ```

## 🔒 Security Hardening

### Firewall Configuration
```bash
# Configure UFW
sudo ufw enable
sudo ufw allow ssh
sudo ufw allow 'Apache Full'
sudo ufw allow 80
sudo ufw allow 443
```

### File Permissions
```bash
# Set proper permissions
sudo chown -R www-data:www-data /var/www/chart
sudo chmod -R 755 /var/www/chart
sudo chmod -R 775 /var/www/chart/storage
sudo chmod -R 775 /var/www/chart/bootstrap/cache
```

### Database Security
```bash
# Secure MySQL
sudo mysql_secure_installation

# Create application user with limited privileges
mysql -u root -p
CREATE USER 'chart_user'@'localhost' IDENTIFIED BY 'strong_password';
GRANT SELECT, INSERT, UPDATE, DELETE ON chart_production.* TO 'chart_user'@'localhost';
FLUSH PRIVILEGES;
```

## 🚀 Performance Optimization

### PHP Optimization
```bash
# Edit PHP configuration
sudo nano /etc/php/8.2/apache2/php.ini

# Optimize these settings:
memory_limit = 256M
max_execution_time = 300
upload_max_filesize = 50M
post_max_size = 50M
opcache.enable = 1
opcache.memory_consumption = 128
opcache.max_accelerated_files = 4000
```

### MySQL Optimization
```bash
# Edit MySQL configuration
sudo nano /etc/mysql/mysql.conf.d/mysqld.cnf

# Add these optimizations:
innodb_buffer_pool_size = 1G
innodb_log_file_size = 256M
max_connections = 200
query_cache_size = 64M
```

## 📈 Scaling Considerations

### Load Balancing
- Use multiple Linode instances
- Set up load balancer
- Implement database replication

### Caching
- Enable Redis for session storage
- Use CDN for static assets
- Implement application-level caching

### Database Scaling
- Set up read replicas
- Implement database sharding
- Use connection pooling

## 🆘 Troubleshooting

### Common Issues

1. **Permission Errors**
   ```bash
   sudo chown -R www-data:www-data /var/www/chart
   sudo chmod -R 755 /var/www/chart
   ```

2. **Database Connection Issues**
   ```bash
   # Check MySQL status
   sudo systemctl status mysql
   
   # Test connection
   mysql -u chart_user -p chart_production
   ```

3. **SSL Certificate Issues**
   ```bash
   # Check certificate status
   sudo certbot certificates
   
   # Renew certificate
   sudo certbot renew
   ```

4. **Performance Issues**
   ```bash
   # Check system resources
   htop
   
   # Check Apache status
   sudo systemctl status apache2
   
   # Check MySQL processes
   mysql -u root -p -e "SHOW PROCESSLIST;"
   ```

## 📞 Support

For deployment issues:
1. Check the logs: `/var/www/chart/storage/logs/laravel.log`
2. Verify configuration: `php artisan config:show`
3. Test database connection: `php artisan tinker`
4. Check service status: `sudo systemctl status apache2 mysql`

## 🎯 Next Steps

After successful deployment:
1. **Test the application** - Verify all features work
2. **Set up monitoring** - Implement application monitoring
3. **Configure backups** - Set up automated backups
4. **Security audit** - Run security scans
5. **Performance testing** - Load test the application
6. **Documentation** - Document your deployment process

Your modern CHaRT application is now ready for production use! 🎉
