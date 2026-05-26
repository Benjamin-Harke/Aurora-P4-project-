# Use PHP 8.2 with Apache
FROM php:8.2-apache

# 1. Install PDO and MySQL drivers for MariaDB
RUN docker-php-ext-install pdo pdo_mysql

# 2. Enable Apache mod_rewrite for MVC routing (.htaccess)
RUN a2enmod rewrite

# 3. Change the Apache DocumentRoot to the /public folder
# This makes sure the website starts in 'public/index.php'
ENV APACHE_DOCUMENT_ROOT /var/www/html/public
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/000-default.conf
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/apache2.conf

# 4. Set the working directory
WORKDIR /var/www/html

# 5. Set permissions so Apache can read the files
RUN chown -R www-data:www-data /var/www/html