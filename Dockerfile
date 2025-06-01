# Dockerfile (PHP-FPM with debug tools)

FROM php:8.3-fpm

# 1️⃣ Instala librerías necesarias para mysqli, PDO y herramientas debug
RUN apt-get update \
 && apt-get install -y --no-install-recommends \
    libmariadb-dev-compat \
    curl \
    net-tools \
    iputils-ping \
    procps \
 && docker-php-ext-install mysqli pdo pdo_mysql \
 && apt-get clean \
 && rm -rf /var/lib/apt/lists/*


# Copia nuestro default.conf al directorio de configuración
COPY default.conf /etc/nginx/conf.d/default.conf

# 2️⃣ Crea la carpeta de sesiones y fija permisos
RUN mkdir -p /var/lib/php/sessions \
 && chown -R www-data:www-data /var/lib/php/sessions

# 3️⃣ Copia el código de la aplicación
WORKDIR /var/www/html
COPY . .
RUN chown -R www-data:www-data .

# 4️⃣ Ajusta el save_path de PHP y el listener de FPM
RUN echo "session.save_path = /var/lib/php/sessions" > /usr/local/etc/php/conf.d/99-sessions.ini \
 && sed -i 's|^listen = .*|listen = 0.0.0.0:9000|' /usr/local/etc/php-fpm.d/www.conf

# 5️⃣ Exponemos el puerto del FPM
EXPOSE 9000

# 6️⃣ Arrancamos PHP-FPM en foreground
CMD ["php-fpm", "-F"]

