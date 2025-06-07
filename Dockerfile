FROM php:8.2-apache

# Instala extensões necessárias
RUN docker-php-ext-install pdo pdo_mysql

# Copia o código para o contêiner
COPY . /var/www/html/

# Define a pasta pública como DocumentRoot
ENV APACHE_DOCUMENT_ROOT /var/www/html/public

# Ajusta o DocumentRoot no Apache
RUN sed -ri -e 's!/var/www/html!/var/www/html/public!g' /etc/apache2/sites-available/000-default.conf

# Habilita mod_rewrite
RUN a2enmod rewrite

EXPOSE 80
