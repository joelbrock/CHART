#!/bin/bash

# CHaRT Modern Deployment Script
# This script automates the deployment process to Linode

set -e  # Exit on any error

echo "🚀 Starting CHaRT Modern Deployment..."

# Colors for output
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
NC='\033[0m' # No Color

# Function to print colored output
print_status() {
    echo -e "${GREEN}[INFO]${NC} $1"
}

print_warning() {
    echo -e "${YELLOW}[WARNING]${NC} $1"
}

print_error() {
    echo -e "${RED}[ERROR]${NC} $1"
}

# Check if running as root
if [[ $EUID -eq 0 ]]; then
   print_error "This script should not be run as root for security reasons"
   exit 1
fi

# Update system packages
print_status "Updating system packages..."
sudo apt update && sudo apt upgrade -y

# Install essential packages
print_status "Installing essential packages..."
sudo apt install -y curl wget git unzip software-properties-common

# Install Apache
print_status "Installing Apache web server..."
sudo apt install -y apache2
sudo systemctl enable apache2
sudo systemctl start apache2

# Install MySQL
print_status "Installing MySQL database server..."
sudo apt install -y mysql-server
sudo systemctl enable mysql
sudo systemctl start mysql

# Install PHP 8.2
print_status "Installing PHP 8.2 and extensions..."
sudo add-apt-repository ppa:ondrej/php -y
sudo apt update
sudo apt install -y php8.2 php8.2-cli php8.2-common php8.2-mysql php8.2-zip \
    php8.2-gd php8.2-mbstring php8.2-curl php8.2-xml php8.2-bcmath \
    php8.2-intl php8.2-xmlrpc php8.2-soap php8.2-readline php8.2-opcache \
    php8.2-ldap php8.2-imagick php8.2-dev php8.2-redis php8.2-memcached

# Install Composer
print_status "Installing Composer..."
if ! command -v composer &> /dev/null; then
    curl -sS https://getcomposer.org/installer | php
    sudo mv composer.phar /usr/local/bin/composer
    sudo chmod +x /usr/local/bin/composer
else
    print_status "Composer already installed"
fi

# Install Node.js
print_status "Installing Node.js..."
if ! command -v node &> /dev/null; then
    curl -fsSL https://deb.nodesource.com/setup_20.x | sudo -E bash -
    sudo apt install -y nodejs
else
    print_status "Node.js already installed"
fi

# Configure Apache
print_status "Configuring Apache..."
sudo a2enmod rewrite
sudo a2enmod ssl
sudo a2enmod headers

# Create application directory
print_status "Setting up application directory..."
sudo mkdir -p /var/www/chart
sudo chown -R $USER:www-data /var/www/chart

# Copy application files
print_status "Copying application files..."
if [ -d "/var/www/chart" ]; then
    print_warning "Application directory already exists. Backing up..."
    sudo mv /var/www/chart /var/www/chart.backup.$(date +%Y%m%d_%H%M%S)
fi

# Copy current directory to /var/www/chart
sudo cp -r . /var/www/chart/
sudo chown -R $USER:www-data /var/www/chart

# Install application dependencies
print_status "Installing application dependencies..."
cd /var/www/chart
composer install --optimize-autoloader --no-dev
npm install
npm run build

# Set up environment file
print_status "Setting up environment configuration..."
if [ ! -f ".env" ]; then
    cp .env.example .env
    print_warning "Please configure your .env file with production settings"
fi

# Set proper permissions
print_status "Setting proper permissions..."
sudo chown -R $USER:www-data /var/www/chart
sudo chmod -R 755 /var/www/chart
sudo chmod -R 775 /var/www/chart/storage
sudo chmod -R 775 /var/www/chart/bootstrap/cache

# Create Apache virtual host
print_status "Creating Apache virtual host..."
sudo tee /etc/apache2/sites-available/chart.conf > /dev/null <<EOF
<VirtualHost *:80>
    ServerName chart.local
    DocumentRoot /var/www/chart/public
    
    <Directory /var/www/chart/public>
        AllowOverride All
        Require all granted
    </Directory>
    
    ErrorLog \${APACHE_LOG_DIR}/chart_error.log
    CustomLog \${APACHE_LOG_DIR}/chart_access.log combined
</VirtualHost>
EOF

# Enable site
sudo a2ensite chart.conf
sudo a2dissite 000-default.conf
sudo systemctl reload apache2

# Configure firewall
print_status "Configuring firewall..."
sudo ufw --force enable
sudo ufw allow ssh
sudo ufw allow 'Apache Full'

# Database setup
print_status "Setting up database..."
print_warning "Please run the following commands to set up your database:"
echo "1. sudo mysql_secure_installation"
echo "2. sudo mysql -u root -p"
echo "3. CREATE DATABASE chart_production;"
echo "4. CREATE USER 'chart_user'@'localhost' IDENTIFIED BY 'your_password';"
echo "5. GRANT ALL PRIVILEGES ON chart_production.* TO 'chart_user'@'localhost';"
echo "6. FLUSH PRIVILEGES;"
echo "7. EXIT;"

# Generate application key
print_status "Generating application key..."
php artisan key:generate

print_status "Deployment script completed!"
print_warning "Next steps:"
echo "1. Configure your .env file with database credentials"
echo "2. Set up your database as shown above"
echo "3. Run: php artisan migrate --force"
echo "4. Run: php artisan db:seed --force"
echo "5. Configure your domain and SSL certificate"
echo "6. Set up monitoring and backups"

echo -e "${GREEN}🎉 CHaRT Modern deployment completed successfully!${NC}"
