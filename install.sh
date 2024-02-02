#!/bin/bash

if ! command -v docker &> /dev/null; then
    echo "Docker is not installed."
    exit 1
fi

if ! command -v docker-compose &> /dev/null; then
    echo "Docker Compose is not installed."
    exit 1
fi

if ! command -v composer &> /dev/null; then
    echo "Installing Composer..."
    EXPECTED_CHECKSUM="$(wget -q -O - https://composer.github.io/installer.sig)"
    php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');"
    ACTUAL_CHECKSUM="$(php -r "echo hash_file('sha384', 'composer-setup.php');")"

    if [ "$EXPECTED_CHECKSUM" != "$ACTUAL_CHECKSUM" ]; then
        >&2 echo "Composer installer checksum mismatch. Aborting."
        rm composer-setup.php
        exit 1
    fi

    php composer-setup.php --quiet
    rm composer-setup.php
fi

echo "Cloning Laravel project..."
git clone https://github.com/your-laravel-repository.git
cd your-laravel-repository || exit

echo "Installing Laravel Sail..."
php composer.phar require laravel/sail --dev
php artisan sail:install --with=mysql,redis

./vendor/bin/sail up -d

echo "Running migrations and seeding the database..."
./vendor/bin/sail artisan migrate:fresh --seed

echo "Laravel application installed successfully!"
