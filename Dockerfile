FROM php:8.3-apache

RUN apt-get update \
 && apt-get install -y --no-install-recommends libpq-dev \
 && docker-php-ext-install pdo_pgsql \
 && a2enmod rewrite headers \
 && sed -ri 's/AllowOverride None/AllowOverride All/' /etc/apache2/apache2.conf \
 && rm -rf /var/lib/apt/lists/*

WORKDIR /var/www/html
COPY . /var/www/html/
RUN chown -R www-data:www-data /var/www/html

# Render fournit la variable PORT (10000 par défaut)
ENV PORT=10000
EXPOSE 10000
CMD sed -ri "s/^Listen .*/Listen ${PORT}/" /etc/apache2/ports.conf \
 && sed -ri "s/<VirtualHost \*:[0-9]+>/<VirtualHost *:${PORT}>/" /etc/apache2/sites-available/000-default.conf \
 && echo "ServerName localhost" >> /etc/apache2/apache2.conf \
 && apache2-foreground
