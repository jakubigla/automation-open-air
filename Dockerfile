FROM php:7.1-fpm

RUN apt-get update && apt-get install -y curl libcurl4-gnutls-dev libyaml-dev
RUN pecl install yaml-2.0.0 && \
    docker-php-ext-enable yaml && \
    docker-php-ext-install curl

WORKDIR /app

CMD [ "php", "./index.php" ]