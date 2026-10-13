FROM php:8.3-apache

RUN apt-get update \
    && apt-get install -y --no-install-recommends libjpeg62-turbo-dev libpng-dev libwebp-dev libsqlite3-dev \
    && docker-php-ext-configure gd --with-jpeg --with-webp \
    && docker-php-ext-install -j"$(nproc)" gd pdo_mysql pdo_sqlite exif \
    && a2enmod rewrite headers expires deflate \
    && rm -rf /var/lib/apt/lists/*

# La racine web pointe sur /public : le code source n'est jamais exposé.
ENV APACHE_DOCUMENT_ROOT=/var/www/html/public
RUN sed -ri 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/*.conf \
    && sed -ri 's!AllowOverride None!AllowOverride All!g' /etc/apache2/apache2.conf \
    && mv "$PHP_INI_DIR/php.ini-production" "$PHP_INI_DIR/php.ini" \
    && printf 'upload_max_filesize=6M\npost_max_size=8M\nexpose_php=Off\n' > "$PHP_INI_DIR/conf.d/app.ini"

WORKDIR /var/www/html
COPY . .
RUN mkdir -p storage public/uploads \
    && chown -R www-data:www-data storage public/uploads

COPY docker/entrypoint.sh /usr/local/bin/entrypoint
RUN chmod +x /usr/local/bin/entrypoint

EXPOSE 80
ENTRYPOINT ["entrypoint"]
CMD ["apache2-foreground"]
