# Use an official PHP 8.2 CLI base image
FROM php:8.4-cli

# Set the working directory inside the container
WORKDIR /application

# Install system dependencies and extensions required by this Laravel project
RUN apt-get update && apt-get install -y \
    git \
    unzip \
    libzip-dev \
    default-mysql-client \
    libpq-dev \
    && docker-php-ext-install zip \
    && apt-get -y autoremove \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/* /tmp/* /var/tmp/*

# Define the PostgreSQL version as a build argument
ARG PG_VERSION

# Install necessary dependencies and add PostgreSQL repository
RUN apt-get update && \
    apt-get install -y wget gnupg2 lsb-release && \
    wget --quiet -O - https://www.postgresql.org/media/keys/ACCC4CF8.asc | apt-key add - && \
    echo "deb http://apt.postgresql.org/pub/repos/apt/ $(lsb_release -cs)-pgdg main" > /etc/apt/sources.list.d/pgdg.list && \
    echo "Package: *\nPin: origin apt.postgresql.org\nPin-Priority: 1001" > /etc/apt/preferences.d/pgdg.pref

# Install the specific PostgreSQL client version
RUN apt-get update && \
    apt-get install -y postgresql-client-$PG_VERSION

# Verify installation
RUN which pg_dump && pg_dump --version

# Clean up
RUN apt-get clean && \
    rm -rf /var/lib/apt/lists/*

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
