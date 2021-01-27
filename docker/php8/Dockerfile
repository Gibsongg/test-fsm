FROM php:8-fpm
RUN apt-get update && apt-get install -y \
        libfreetype6-dev \
        libjpeg62-turbo-dev \
        libpng-dev \
        graphviz
    #&& docker-php-ext-configure gd --with-freetype --with-jpeg \
    #&& docker-php-ext-install -j$(nproc) gd \


RUN docker-php-ext-install \
    pdo \
    pdo_mysql \
    gd

RUN apt-get install -y git

RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin/ --filename=composer

WORKDIR /var/www
