# ---------- Dockerfile (corrigido) ----------
FROM php:8.2-fpm

# 1. Paquetes y extensiones
RUN apt-get update && apt-get install -y \
        nginx supervisor default-mysql-client default-libmysqlclient-dev \
    && docker-php-ext-install mysqli \
    && docker-php-ext-enable  mysqli \
    && rm -rf /var/lib/apt/lists/*

# 2. Directorios que Nginx necesita
RUN mkdir -p /var/run /var/cache/nginx /var/log/nginx

# 3. Código de la aplicación
COPY . /var/www/html
RUN chown -R www-data:www-data /var/www/html

# 4. Configuración
#    Nginx leerá todos los ficheros en /etc/nginx/conf.d/
COPY default.conf      /etc/nginx/conf.d/default.conf
#    Supervisord arranca nginx + php-fpm
COPY supervisord.conf  /etc/supervisor/conf.d/supervisord.conf

# 5. Arranque
EXPOSE 80
CMD ["supervisord","-c","/etc/supervisor/conf.d/supervisord.conf"]
# --------------------------------------------
