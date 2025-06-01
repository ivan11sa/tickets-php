#!/bin/bash

# Arranca php-fpm en background
php-fpm -D

# Arranca nginx en foreground (mantiene el contenedor vivo)
nginx -g 'daemon off;'
