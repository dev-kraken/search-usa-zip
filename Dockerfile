FROM php:8.3-fpm

# Install system dependencies
RUN apt-get update && apt-get install -y \
    git \
    curl \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    zip \
    unzip

# Clear cache
RUN apt-get clean && rm -rf /var/lib/apt/lists/*

# Install PHP extensions
RUN docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd

# Install Redis extension if not already installed
RUN if ! [ -e /usr/local/etc/php/conf.d/docker-php-ext-redis.ini ]; then \
    pecl install redis && \
    docker-php-ext-enable redis \
    ; fi

# Get latest Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Set working directory
WORKDIR /var/www

# Copy existing application directory contents
COPY . /var/www

# Install dependencies
RUN composer install

# Change ownership of our applications
RUN chown -R www-data:www-data /srv/http/search_zip

# Expose port 9000 and start php-fpm server
EXPOSE 9000
CMD ["php-fpm"]