# Usar PHP 8.2 com Apache
FROM php:8.2-apache

# Instalar extensões PHP necessárias
RUN docker-php-ext-install mysqli

# Ativar mod_rewrite do Apache
RUN a2enmod rewrite

# Copiar todo o backend para o diretório raiz do Apache
COPY backend/ /var/www/html/

# Copiar frontend para pasta pública do Apache
COPY frontend/ /var/www/html/

# Definir permissões para o usuário www-data
RUN chown -R www-data:www-data /var/www/html/

# Expor porta 80 para acessar o servidor Apache
EXPOSE 80

# Comando padrão para rodar Apache no primeiro plano
CMD ["apache2-foreground"]
