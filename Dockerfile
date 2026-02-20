FROM php:8.2-apache
# Instalamos extensiones comunes para bases de datos
RUN docker-php-ext-install mysqli pdo pdo_mysql
# Copiamos tu código al servidor
COPY . /var/www/html/
# Damos permisos
RUN chown -R www-data:www-data /var/www/html
EXPOSE 80
