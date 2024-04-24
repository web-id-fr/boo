# Use an official PHP 8.2 CLI base image
FROM php:8.2-cli

# Set the working directory inside the container
WORKDIR /application

# Install system dependencies and extensions required by this Laravel project
RUN apt-get update && apt-get install -y \
    git \
    unzip \
    libzip-dev \
    default-mysql-client \
    postgresql \
    libpq-dev \
    && docker-php-ext-install zip pdo_pgsql pgsql \
    && apt-get -y autoremove \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/* /tmp/* /var/tmp/*

# Install Composer globally
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# Install rclone
RUN curl https://rclone.org/install.sh | bash

# Copy the Laravel project files into the container
COPY . /application

# Install Laravel project dependencies using Composer
RUN composer install --no-dev

# Expose a port if your Laravel project uses a specific port for Artisan commands
# EXPOSE 8000

# The command to run Laravel Artisan
CMD ["php", "artisan", "list"]
