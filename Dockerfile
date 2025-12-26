FROM cr.yandex/crp1837ivv9r1fj8r48u/php:8.3-laravel

COPY . /var/www/back/html

RUN apt-get update; apt-get install default-libmysqlclient-dev -y; apt-get clear; rm -rf /var/lib/apt/lists/*
RUN docker-php-ext-install pdo pdo_mysql

RUN composer clear-cache; \
    composer dump-autoload; \
    composer install
RUN chown www-data:www-data -R /var/www/
