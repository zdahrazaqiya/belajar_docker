FROM php:8.2-apache AS builder

WORKDIR /var/www/html
COPY . /var/www/html/

FROM php:8.2-apache AS runtime

ENV APACHE_DOCUMENT_ROOT=/var/www/html \
    PHP_OPCACHE_ENABLE=1

RUN docker-php-ext-install mysqli opcache \
    && a2enmod rewrite headers expires \
    && mkdir -p /var/www/html/assets/img \
    && chown -R www-data:www-data /var/www/html

WORKDIR /var/www/html
COPY --from=builder --chown=www-data:www-data /var/www/html/ /var/www/html/
COPY --chown=root:root docker/php/opcache.ini /usr/local/etc/php/conf.d/opcache.ini

EXPOSE 80

HEALTHCHECK --interval=30s --timeout=5s --start-period=10s --retries=3 \
    CMD php -r '$f=@fsockopen("127.0.0.1", 80, $e, $s, 2); exit($f ? 0 : 1);'
