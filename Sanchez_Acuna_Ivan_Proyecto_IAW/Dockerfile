# Usa una imagen base oficial de PHP
FROM php:8.2-fpm

# Instala dependencias necesarias
RUN apt-get update && apt-get install -y \
    nginx \
    supervisor \
    default-mysql-client \
    default-libmysqlclient-dev \
    && docker-php-ext-install mysqli \
    && docker-php-ext-enable mysqli \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/*

# Copia los archivos del proyecto al contenedor
COPY . /var/www/html/

# Copia la configuración de nginx y supervisord
COPY ./Sanchez_Acuna_Ivan_Proyecto_IAW/default.conf /etc/nginx/sites-available/default
COPY ./Sanchez_Acuna_Ivan_Proyecto_IAW/supervisord.conf /etc/supervisor/conf.d/supervisord.conf

# Cambia los permisos para los archivos de proyecto
RUN chown -R www-data:www-data /var/www/html

# Expón el puerto 80
EXPOSE 80



CMD ["/usr/bin/supervisord", "-n"]
