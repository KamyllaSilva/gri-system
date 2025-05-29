FROM php:8.2-apache

# Instala extensões necessárias
RUN docker-php-ext-install pdo pdo_mysql

# Ativa mod_rewrite
RUN a2enmod rewrite

# Copia o código para a pasta padrão do apache
COPY . /var/www/html/

# Define permissões para uploads
RUN mkdir -p /var/www/html/uploads && chown -R www-data:www-data /var/www/html/uploads

# Configura permissão escrita para a pasta uploads
RUN chmod -R 755 /var/www/html/uploads

# Exponha porta 8080 (Railway usa 8080)
EXPOSE 8080

# Apache rodando em foreground
CMD ["apache2-foreground"]
