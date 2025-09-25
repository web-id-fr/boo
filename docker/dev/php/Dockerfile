# First, declare all ARGs at the top with default values
ARG UBUNTU_IMAGE=ubuntu:jammy
ARG COMPOSER_IMAGE=composer:2.8.6

# Create composer stage first
FROM ${COMPOSER_IMAGE} AS composer

# Then create the base cli image
FROM ${UBUNTU_IMAGE} AS cli

ARG WWWUSER
ARG WWWGROUP
ARG PHP_VERSION=8.4
ARG PG_VERSION=16

# Fixes some weird terminal issues such as broken clear / CTRL+L
ENV TERM=linux

# Ensure apt doesn't ask questions when installing stuff
ENV DEBIAN_FRONTEND=noninteractive

# Install Ondrej repos for Ubuntu jammy, PHP, composer and selected extensions - better selection than
# the distro's packages
RUN apt-get update \
    && apt-get install -y --no-install-recommends gnupg \
    && echo "deb http://ppa.launchpad.net/ondrej/php/ubuntu jammy main" > /etc/apt/sources.list.d/ondrej-php.list \
    && apt-key adv --keyserver keyserver.ubuntu.com --recv-keys 4F4EA0AAE5267A6C \
    && apt-get update \
    && apt-get -y --no-install-recommends install \
        ca-certificates \
        git \
        curl \
        unzip \
        default-mysql-client \
        php${PHP_VERSION}-apcu \
        php${PHP_VERSION}-cli \
        php${PHP_VERSION}-curl \
        php${PHP_VERSION}-mbstring \
        php${PHP_VERSION}-opcache \
        php${PHP_VERSION}-readline \
        php${PHP_VERSION}-xml \
        php${PHP_VERSION}-zip \
        php${PHP_VERSION}-gd \
        php${PHP_VERSION}-redis \
        php${PHP_VERSION}-bcmath \
        php${PHP_VERSION}-intl \
        php${PHP_VERSION}-pgsql \
    && apt-get -y autoremove \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/* /tmp/* /var/tmp/* /usr/share/doc/*

# Copy composer from the dedicated stage
COPY --from=composer /usr/bin/composer /usr/bin/composer

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

# Install rclone
RUN curl https://rclone.org/install.sh | bash

CMD ["php", "-a"]

RUN groupadd --force -g $WWWGROUP web
RUN useradd -ms /bin/bash --no-user-group -g $WWWGROUP -u $WWWUSER web

USER web

WORKDIR "/application"
