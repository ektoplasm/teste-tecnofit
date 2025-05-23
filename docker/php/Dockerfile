FROM php:8.4-fpm-alpine

# Diretório de trabalho
WORKDIR /var/www/html

# Instalação de dependências do sistema
RUN apk --no-cache add \
    autoconf \
    build-base \
    freetype \
      freetype-dev \
      libjpeg-turbo-dev \
      libpng-dev \
    libxpm-dev \
    libzip-dev \
    zip \
    unzip \
    git \
    bash \
    fcgi \
    libmcrypt-dev \
    oniguruma-dev \
    zlib

# Limpando cache
RUN rm -rf /var/cache/apk/*

# Instalação de extensões do PHP
RUN pecl install -f \
    && docker-php-ext-install pdo pdo_mysql mbstring zip exif pcntl bcmath opcache

RUN find /var/www/html -type f -exec chmod 644 {} \; \
    && find /var/www/html -type d -exec chmod 755 {} \;

# Abrindo porta 9000 do fpm
EXPOSE 9000

CMD ["php-fpm"]