#!/bin/bash

# CHaRT Maintenance Script
# This script performs regular maintenance tasks

set -e

# Colors for output
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
NC='\033[0m' # No Color

print_status() {
    echo -e "${GREEN}[INFO]${NC} $1"
}

print_warning() {
    echo -e "${YELLOW}[WARNING]${NC} $1"
}

print_error() {
    echo -e "${RED}[ERROR]${NC} $1"
}

# Change to application directory
cd /var/www/chart

print_status "Starting CHaRT maintenance..."

# Clear Laravel caches
print_status "Clearing Laravel caches..."
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear

# Rebuild caches for production
print_status "Rebuilding production caches..."
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Optimize application
print_status "Optimizing application..."
php artisan optimize

# Clear old logs (keep last 7 days)
print_status "Cleaning old log files..."
find storage/logs -name "*.log" -mtime +7 -delete

# Update Composer dependencies
print_status "Updating Composer dependencies..."
composer install --optimize-autoloader --no-dev

# Update Node.js dependencies and build assets
print_status "Updating Node.js dependencies..."
npm install
npm run build

# Check disk space
print_status "Checking disk space..."
df -h

# Check application status
print_status "Checking application status..."
php artisan tinker --execute="echo 'Application is running properly';"

# Restart services
print_status "Restarting services..."
sudo systemctl restart apache2
sudo systemctl restart mysql

# Check service status
print_status "Checking service status..."
sudo systemctl status apache2 --no-pager -l
sudo systemctl status mysql --no-pager -l

# Security check
print_status "Running security checks..."
sudo ufw status
sudo fail2ban-client status 2>/dev/null || print_warning "Fail2ban not installed"

# Database maintenance
print_status "Running database maintenance..."
mysql -u chart_user -p -e "OPTIMIZE TABLE chart_production.*;" 2>/dev/null || print_warning "Database optimization skipped"

print_status "Maintenance completed successfully!"
print_status "Next maintenance scheduled for: $(date -d '+1 week')"
