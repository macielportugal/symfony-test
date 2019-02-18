FROM php:7-fpm

RUN apt-get update && \
    apt-get install -y git zip libpq-dev curl openssl && \
    docker-php-ext-install pdo pdo_pgsql && \
    apt-get clean all

RUN php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');" && \
    php composer-setup.php --install-dir=/usr/local/bin --filename=composer && \
    php -r "unlink('composer-setup.php');"
