#!/bin/bash

# CHaRT Backup Script
# This script creates backups of the database and application files

set -e

# Configuration
BACKUP_DIR="/var/backups/chart"
DATE=$(date +%Y%m%d_%H%M%S)
DB_NAME="chart_production"
DB_USER="chart_user"
APP_DIR="/var/www/chart"

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

# Create backup directory
print_status "Creating backup directory..."
sudo mkdir -p $BACKUP_DIR
sudo chown -R $USER:$USER $BACKUP_DIR

# Database backup
print_status "Creating database backup..."
mysqldump -u $DB_USER -p $DB_NAME > $BACKUP_DIR/database_$DATE.sql
gzip $BACKUP_DIR/database_$DATE.sql

# Application backup (excluding unnecessary files)
print_status "Creating application backup..."
tar -czf $BACKUP_DIR/application_$DATE.tar.gz \
    --exclude='node_modules' \
    --exclude='vendor' \
    --exclude='storage/logs' \
    --exclude='storage/framework/cache' \
    --exclude='storage/framework/sessions' \
    --exclude='storage/framework/views' \
    --exclude='.git' \
    -C /var/www chart

# Create backup info file
print_status "Creating backup information..."
cat > $BACKUP_DIR/backup_info_$DATE.txt << EOF
CHaRT Backup Information
=======================
Date: $(date)
Database: $DB_NAME
Application: $APP_DIR
Backup Directory: $BACKUP_DIR

Files Created:
- database_$DATE.sql.gz
- application_$DATE.tar.gz
- backup_info_$DATE.txt

To restore database:
gunzip $BACKUP_DIR/database_$DATE.sql.gz
mysql -u $DB_USER -p $DB_NAME < $BACKUP_DIR/database_$DATE.sql

To restore application:
tar -xzf $BACKUP_DIR/application_$DATE.tar.gz -C /var/www/
EOF

# Clean old backups (keep 7 days)
print_status "Cleaning old backups..."
find $BACKUP_DIR -name "*.sql.gz" -mtime +7 -delete
find $BACKUP_DIR -name "*.tar.gz" -mtime +7 -delete
find $BACKUP_DIR -name "backup_info_*.txt" -mtime +7 -delete

# Set proper permissions
sudo chown -R $USER:$USER $BACKUP_DIR
sudo chmod -R 755 $BACKUP_DIR

print_status "Backup completed successfully!"
print_status "Backup location: $BACKUP_DIR"
print_status "Files created:"
ls -la $BACKUP_DIR/*$DATE*

# Optional: Upload to cloud storage
# Uncomment and configure if you want to upload backups to cloud storage
# print_status "Uploading to cloud storage..."
# aws s3 cp $BACKUP_DIR/database_$DATE.sql.gz s3://your-bucket/chart/backups/
# aws s3 cp $BACKUP_DIR/application_$DATE.tar.gz s3://your-bucket/chart/backups/
