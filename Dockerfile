# Usa PHP-FPM como base
FROM php:8.3-fpm

# 1️⃣ Instala librerías necesarias para mysqli, PDO y herramientas debug + nginx
RUN apt-get update \
 && apt-get install -y --no-install-recommends \
    libmariadb-dev-compat \
    nginx \
    curl \
    net-tools \
    iputils-ping \
    procps \
 && docker-php-ext-install mysqli pdo pdo_mysql \
 && apt-get clean \
 && rm -rf /var/lib/apt/lists/*

# 2️⃣ Copia nuestro default.conf al directorio de configuración de nginx
COPY default.conf /etc/nginx/conf.d/default.conf

# 3️⃣ Crea la carpeta de sesiones y fija permisos
RUN mkdir -p /var/lib/php/sessions \
 && chown -R www-data:www-data /var/lib/php/sessions

# 4️⃣ Copia el código de la aplicación
WORKDIR /var/www/html
COPY . .
RUN chown -R www-data:www-data .

# 5️⃣ Ajusta el save_path de PHP y el listener de FPM
RUN echo "session.save_path = /var/lib/php/sessions" > /usr/local/etc/php/conf.d/99-sessions.ini \
 && sed -i 's|^listen = .*|listen = 127.0.0.1:9000|' /usr/local/etc/php-fpm.d/www.conf

 RUN rm /etc/nginx/sites-enabled/default

# 6️⃣ Copia un script de arranque que levante ambos servicios
COPY start.sh /start.sh
RUN chmod +x /start.sh

# 7️⃣ Expone el puerto HTTP
EXPOSE 80

# 8️⃣ Lanza nginx + php-fpm juntos
CMD ["/start.sh"]
