FROM php:8.2-apache
RUN docker-php-ext-install pdo pdo_mysql
RUN a2enmod rewrite
COPY . /var/www/html/
RUN mkdir -p /var/www/html/uploads && chown -R www-data:www-data /var/www/html/uploads
RUN chmod -R 755 /var/www/html/uploads
EXPOSE 8080
CMD ["apache2-foreground"]
