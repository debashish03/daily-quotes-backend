#!/bin/bash

# Laravel Daily Quotes Backend - Server Setup Script
# For server: 43.230.203.228

set -e

echo "🚀 Setting up server for Laravel Daily Quotes Backend..."

# Update system
echo "📦 Updating system packages..."
apt update && apt upgrade -y

# Install required packages
echo "🔧 Installing required packages..."
apt install -y apache2 php8.1 php8.1-cli php8.1-common php8.1-mysql php8.1-zip php8.1-gd php8.1-mbstring php8.1-curl php8.1-xml php8.1-bcmath php8.1-json php8.1-tokenizer php8.1-fileinfo php8.1-opcache php8.1-mysqli composer mysql-server git

# Start and enable services
echo "🔧 Starting services..."
systemctl start apache2
systemctl enable apache2
systemctl start mysql
systemctl enable mysql

# Create application directory
echo "📁 Creating application directory..."
mkdir -p /var/www/daily-quotes-backend
chown -R www-data:www-data /var/www/daily-quotes-backend

# Configure Apache
echo "🌐 Configuring Apache..."
a2enmod rewrite
systemctl reload apache2

# Set up MySQL
echo "🗄️ Setting up MySQL..."
mysql -e "CREATE DATABASE IF NOT EXISTS daily_quotes;"
mysql -e "CREATE USER IF NOT EXISTS 'daily_quotes_user'@'localhost' IDENTIFIED BY 'DailyQuotes2024!';"
mysql -e "GRANT ALL PRIVILEGES ON daily_quotes.* TO 'daily_quotes_user'@'localhost';"
mysql -e "FLUSH PRIVILEGES;"

# Configure firewall
echo "🔥 Configuring firewall..."
ufw allow 22
ufw allow 80
ufw allow 443
ufw --force enable

echo "✅ Server setup completed!"
echo "📋 Next steps:"
echo "1. Configure Jenkins pipeline"
echo "2. Set up SSH keys for Jenkins"
echo "3. Run your first deployment"
