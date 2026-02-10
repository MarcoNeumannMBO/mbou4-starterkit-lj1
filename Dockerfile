# Simpele PHP + Apache image voor beginners
# We installeren pdo_mysql zodat PDO met MySQL werkt.

FROM php:8.2-apache

# Installeer pdo_mysql (nodig voor PDO + MySQL)
RUN docker-php-ext-install pdo_mysql

# (Optioneel) nette Apache instellingen, niet nodig voor dit simpele project
# WORKDIR is standaard /var/www/html in dit image
