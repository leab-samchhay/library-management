# ជ្រើសរើសប្រភេទ PHP (អាចប្តូរទៅ 8.3 បើ Laravel របស់អ្នកទាមទារ)
FROM php:8.3-fpm

# ដំឡើងកម្មវិធីដែលចាំបាច់សម្រាប់ Server
RUN apt-get update -y && apt-get install -y \
    libzip-dev \
    zip \
    unzip \
    git \
    curl

# ដំឡើង PHP Extensions សម្រាប់ភ្ជាប់ Database
RUN docker-php-ext-install pdo pdo_mysql zip

# ដំឡើង Composer សម្រាប់គ្រប់គ្រង Package របស់ Laravel
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# កំណត់ទីតាំងធ្វើការងារ
WORKDIR /app

# ចម្លងកូដ Laravel ទាំងអស់ចូលទៅក្នុង Docker
COPY . .

# ដំឡើង Packages របស់ Laravel
RUN composer install --no-interaction --optimize-autoloader --no-dev

# បើក Port 10000 (Render តម្រូវឱ្យប្រើ Port នេះ)
EXPOSE 10000

# បញ្ជាឱ្យ Laravel Server ដំណើរការនៅពេល Deploy ចប់
# CMD php artisan serve --host=0.0.0.0 --port=10000
# ឧទាហរណ៍នៅក្នុង Dockerfile របស់អ្នក (ត្រង់កន្លែង Startup)
CMD php artisan config:cache && php artisan route:cache && php artisan storage:link && apache2-foreground
