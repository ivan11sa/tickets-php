# Dockerfile (PHP-FPM only)

FROM php:8.3-fpm

# 1) Instala la librería de MariaDB para mysqli y PDO
RUN apt-get update \
 && apt-get install -y --no-install-recommends libmariadb-dev-compat \
 && docker-php-ext-install mysqli pdo pdo_mysql \
 && rm -rf /var/lib/apt/lists/*

# 2) Crea la carpeta de sesiones y fija permisos
RUN mkdir -p /var/lib/php/sessions \
 && chown -R www-data:www-data /var/lib/php/sessions

# 3) Copia el código de la aplicación
COPY . /var/www/html
RUN chown -R www-data:www-data /var/www/html

# 4) Ajusta el save_path de PHP y el listener de FPM
RUN echo "session.save_path = /var/lib/php/sessions" > /usr/local/etc/php/conf.d/99-sessions.ini \
 && sed -i 's|^listen = .*|listen = 0.0.0.0:9000|' /usr/local/etc/php-fpm.d/www.conf

# 5) Exponemos sólo FPM
EXPOSE 9000

# 6) Arrancamos PHP-FPM
CMD ["php-fpm"]
