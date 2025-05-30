# Dockerfile

FROM php:8.3-fpm

# 1) Instala Nginx, Supervisor y mysqli
RUN apt-get update \
    && apt-get install -y --no-install-recommends \
         nginx \
         supervisor \
         libmysqlclient-dev \
    && docker-php-ext-install mysqli pdo pdo_mysql \
    && rm -rf /var/lib/apt/lists/*

# 2) Crear carpetas y permisos
RUN mkdir -p /var/run/nginx /var/log/nginx /var/lib/php/sessions \
    && chown -R www-data:www-data /var/lib/php/sessions

# 3) Copiar código de la aplicación
COPY . /var/www/html
RUN chown -R www-data:www-data /var/www/html

# 4) Configuración de Nginx (toma default.conf de la raíz)
COPY default.conf /etc/nginx/conf.d/default.conf :contentReference[oaicite:0]{index=0}

# 5) Configuración de Supervisor
COPY supervisord.conf /etc/supervisor/supervisord.conf :contentReference[oaicite:1]{index=1}

# 6) Ajuste de sesiones PHP
RUN echo "session.save_path = /var/lib/php/sessions" \
    >> /usr/local/etc/php/conf.d/99-sessions.ini

# 7) Exponer HTTP y arrancar ambos servicios
EXPOSE 80
CMD ["supervisord", "-c", "/etc/supervisor/supervisord.conf"]
