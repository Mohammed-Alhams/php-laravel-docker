# Use a specific version of the base image
FROM richarvey/nginx-php-fpm:2.1.2

# Set the working directory inside the container
WORKDIR /var/www/html

# Copy only necessary files to the container (assuming you have a .dockerignore file)
COPY . .

# Image config
ENV SKIP_COMPOSER 1
ENV WEBROOT /var/www/html/public
ENV PHP_ERRORS_STDERR 1
ENV RUN_SCRIPTS 1
ENV REAL_IP_HEADER 1

# Laravel config
ENV APP_ENV production
ENV APP_DEBUG false
ENV LOG_CHANNEL stderr

# Allow composer to run as root
ENV COMPOSER_ALLOW_SUPERUSER 1

RUN apt-get update && \
    apt-get install -y && \
    apt-get autoremove --purge && \
    apt-get -y clean



# If you need to install additional PHP extensions or other dependencies, do it here
# For example, to install the PDO extension, uncomment the following line:
# RUN docker-php-ext-install pdo pdo_mysql

# Expose ports if required (adjust according to your application's needs)
EXPOSE 80
EXPOSE 443

# Set the command to run when the container starts
CMD ["/start.sh"]
