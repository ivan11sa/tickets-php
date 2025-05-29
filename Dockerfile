# ---------- Dockerfile (corrigido) ----------
FROM php:8.2-fpm

# 1. Paquetes y extensiones
RUN apt-get update && apt-get install -y \
        nginx supervisor default-mysql-client default-libmysqlclient-dev \
    && docker-php-ext-install mysqli \
    && docker-php-ext-enable mysqli \
    && rm -rf /var/lib/apt/lists/*

# 2. Sesiones PHP
RUN mkdir -p /var/lib/php/sessions \
    && chown -R www-data:www-data /var/lib/php/sessions

# 3. Directorios de Nginx
RUN mkdir -p /var/run /var/cache/nginx /var/log/nginx

# 4. Código de la aplicación
COPY Sanchez_Acuna_Ivan_Proyecto_IAW/ /var/www/html
RUN chown -R www-data:www-data /var/www/html

# 5. Configuración de Nginx y Supervisor
COPY default.conf /etc/nginx/conf.d/default.conf
RUN rm -f /etc/nginx/sites-enabled/default
COPY supervisord.conf /etc/supervisor/conf.d/supervisord.conf

# 6. Ajuste de php.ini
RUN echo "session.save_path = /var/lib/php/sessions" \
         >> /usr/local/etc/php/conf.d/99-custom.ini

# 7. Arranque
EXPOSE 80
CMD ["supervisord","-c","/etc/supervisor/conf.d/supervisord.conf"]
# --------------------------------------------
