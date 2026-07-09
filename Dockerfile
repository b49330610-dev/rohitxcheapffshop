FROM php:8.2-apache

RUN apt-get update && apt-get install -y sqlite3 libsqlite3-dev
RUN docker-php-ext-install pdo_sqlite
RUN a2enmod rewrite

COPY . /var/www/html/
RUN chmod -R 777 /var/www/html

EXPOSE 8080

# Apache ko port 8080 pe run karne ke liye
RUN sed -i 's/80/8080/g' /etc/apache2/ports.conf
RUN sed -i 's/:80/:8080/g' /etc/apache2/sites-available/000-default.conf

CMD ["apache2-foreground"]
