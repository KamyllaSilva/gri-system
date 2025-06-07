FROM php:8.2-apache

# Instalar extensões necessárias (MySQL, etc.)
RUN docker-php-ext-install mysqli

# Ativar mod_rewrite do Apache (para rotas limpas, se usar)
RUN a2enmod rewrite

# Copiar todos os arquivos para o diretório raiz do Apache
COPY . /var/www/html/

# Definir permissões
RUN chown -R www-data:www-data /var/www/html/

EXPOSE 80
