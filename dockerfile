FROM php:8.2-apache

# COPIA os arquivos do projeto para dentro do Apache
COPY . /var/www/html/

# Habilita as extensões necessárias
RUN docker-php-ext-install pdo pdo_mysql

# FORÇA o Apache a escutar na porta 80 (padrão HTTP)
EXPOSE 80
