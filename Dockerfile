# 1) Base: PHP-FPM oficial
FROM php:8.3-fpm

# 2) Instalar Nginx, Supervisor y extensiones PHP necesarias
RUN apt-get update \
    && apt-get install -y --no-install-recommends \
         nginx \
         supervisor \
         libmysqlclient-dev \
    && docker-php-ext-install mysqli pdo pdo_mysql \
    && rm -rf /var/lib/apt/lists/*

# 3) Crear carpetas necesarias y ajustar permisos
RUN mkdir -p /var/run/nginx /var/log/nginx /var/lib/php/sessions \
    && chown -R www-data:www-data /var/lib/php/sessions

# 4) Copiar código de la aplicación
COPY . /var/www/html
RUN chown -R www-data:www-data /var/www/html

# 5) Copiar configuración de Nginx
COPY docker/nginx/default.conf /etc/nginx/conf.d/default.conf
RUN rm -f /etc/nginx/conf.d/default.conf.default

# 6) Copiar configuración de Supervisor
COPY docker/supervisor/supervisord.conf /etc/supervisor/supervisord.conf

# 7) Ajustes PHP
RUN echo "session.save_path = /var/lib/php/sessions" \
    >> /usr/local/etc/php/conf.d/99-sessions.ini

# 8) Exponer puerto HTTP
EXPOSE 80

# 9) Arrancar Supervisord
CMD ["supervisord", "-c", "/etc/supervisor/supervisord.conf"]
