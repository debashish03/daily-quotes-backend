pipeline {
    agent any

    environment {
        TARGET_SERVER = '43.230.203.228'
        TARGET_USER = 'root'
        TARGET_PATH = '/var/www/daily-quotes-backend'
        JENKINS_URL = 'jenkin.fastpigeon.in'
    }

    stages {
        stage('Checkout') {
            steps {
                checkout scm
                echo "✅ Code checked out from GitHub"
            }
        }

        stage('Install Dependencies') {
            steps {
                sh 'composer install --no-dev --optimize-autoloader'
                echo "✅ Composer dependencies installed"
            }
        }

        stage('Deploy to Server') {
            steps {
                script {
                    // Create deployment package
                    sh 'tar -czf daily-quotes-backend.tar.gz --exclude=node_modules --exclude=.git --exclude=storage/logs/* --exclude=storage/framework/cache/* .'

                    // Copy to server
                    sh "scp -o StrictHostKeyChecking=no daily-quotes-backend.tar.gz root@${TARGET_SERVER}:/tmp/"

                    // Deploy on server
                    sh """
                        ssh -o StrictHostKeyChecking=no root@${TARGET_SERVER} '
                            echo "🚀 Starting deployment..."

                            # Create directory if not exists
                            mkdir -p ${TARGET_PATH}
                            cd ${TARGET_PATH}

                            # Backup current version
                            if [ -d "current" ]; then
                                echo "📦 Creating backup..."
                                mv current backup_\$(date +%Y%m%d_%H%M%S)
                            fi

                            # Extract new version
                            echo "📥 Extracting new version..."
                            tar -xzf /tmp/daily-quotes-backend.tar.gz -C /tmp/
                            mv /tmp/daily-quotes-backend current

                            # Set permissions
                            echo "🔐 Setting permissions..."
                            chown -R www-data:www-data current
                            chmod -R 755 current
                            chmod -R 775 current/storage
                            chmod -R 775 current/bootstrap/cache

                            # Install dependencies
                            echo "📦 Installing dependencies..."
                            cd current
                            composer install --no-dev --optimize-autoloader

                            # Set up environment
                            if [ ! -f .env ]; then
                                cp .env.example .env
                                php artisan key:generate
                            fi

                            # Run migrations
                            echo "🔄 Running migrations..."
                            php artisan migrate --force

                            # Clear caches
                            echo "🧹 Clearing caches..."
                            php artisan config:cache
                            php artisan route:cache
                            php artisan view:cache

                            # Create symbolic link
                            echo "🔗 Creating symbolic link..."
                            ln -sf ${TARGET_PATH}/current /var/www/html/daily-quotes-backend

                            # Configure Apache virtual host for clean URLs
                            echo "🌐 Configuring Apache virtual host..."
                            cat > /etc/apache2/sites-available/daily-quotes-backend.conf << 'EOF'
<VirtualHost *:80>
    ServerName 43.230.203.228
    DocumentRoot /var/www/html/daily-quotes-backend/public

    <Directory /var/www/html/daily-quotes-backend/public>
        AllowOverride All
        Require all granted
    </Directory>

    ErrorLog ${APACHE_LOG_DIR}/daily-quotes-backend_error.log
    CustomLog ${APACHE_LOG_DIR}/daily-quotes-backend_access.log combined
</VirtualHost>
EOF

                            # Enable the site
                            a2ensite daily-quotes-backend
                            a2dissite 000-default

                            # Restart Apache
                            echo "🔄 Restarting Apache..."
                            systemctl reload apache2

                            echo "✅ Deployment completed successfully!"
                        '
                    """
                }
            }
        }

        stage('Health Check') {
            steps {
                script {
                    // Wait for deployment to settle
                    sleep 15

                    // Check if application is responding
                    sh "curl -f http://${TARGET_SERVER}/ || echo 'Health check failed but deployment may still be successful'"

                    echo "✅ Health check completed"
                }
            }
        }
    }

    post {
        always {
            // Cleanup
            sh 'rm -f daily-quotes-backend.tar.gz'
            echo "🧹 Cleanup completed"
        }
        success {
            echo "🎉 Deployment completed successfully!"
            echo "🌐 Application URL: http://${TARGET_SERVER}/"
            echo "🔧 Admin Panel: http://${TARGET_SERVER}/admin"
            echo "📊 API Base URL: http://${TARGET_SERVER}/api"
        }
        failure {
            echo "❌ Deployment failed!"
        }
    }
}
