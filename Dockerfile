# Use an official PHP 8.4 CLI base image
FROM php:8.4-cli

# Set the working directory inside the container
WORKDIR /application

# Define the PostgreSQL version as a build argument
ARG PG_VERSION=16

# Use the default production configuration
RUN mv "$PHP_INI_DIR/php.ini-production" "$PHP_INI_DIR/php.ini"

# Install all system dependencies and PostgreSQL client in one layer
RUN apt-get update && apt-get install -y \
    git \
    unzip \
    libzip-dev \
    default-mysql-client \
    libpq-dev \
    wget \
    gnupg \
    lsb-release \
    && docker-php-ext-install zip \
    && mkdir -p /etc/apt/keyrings \
    && wget --quiet -O - https://www.postgresql.org/media/keys/ACCC4CF8.asc | gpg --dearmor -o /etc/apt/keyrings/postgresql.gpg \
    && echo "deb [signed-by=/etc/apt/keyrings/postgresql.gpg] http://apt.postgresql.org/pub/repos/apt/ $(lsb_release -cs)-pgdg main" > /etc/apt/sources.list.d/pgdg.list \
    && apt-get update \
    && apt-get install -y postgresql-client-$PG_VERSION \
    && apt-get -y autoremove \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/* /tmp/* /var/tmp/*

# Verify installation
RUN which pg_dump && pg_dump --version

# Install Composer globally
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# Install rclone
RUN curl -s https://rclone.org/install.sh | bash

# Copy the Laravel project files into the container
COPY . /application

# Install Laravel project dependencies using Composer
RUN composer install --no-dev --optimize-autoloader

# The command to run Laravel Artisan
CMD ["php", "artisan", "list"]
