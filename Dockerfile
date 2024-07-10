FROM php:7.4-apache
RUN docker-php-ext-install mysqli
COPY . /var/www/html
EXPOSE 80
CMD ["apache2-foreground"]
