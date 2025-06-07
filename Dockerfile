# Usa imagem do PHP com Apache embutido
FROM php:8.2-apache

# Instala extensões necessárias
RUN docker-php-ext-install pdo pdo_mysql

# Define nova DocumentRoot
ENV APACHE_DOCUMENT_ROOT /var/www/html/public

# Atualiza VirtualHost para apontar pra 'public'
RUN sed -ri -e 's!/var/www/html!/var/www/html/public!g' /etc/apache2/sites-available/000-default.conf

# Ativa mod_rewrite (opcional para URLs amigáveis)
RUN a2enmod rewrite

# Copia os arquivos do projeto para o container
COPY . /var/www/html

# Expõe a porta 80
EXPOSE 80
