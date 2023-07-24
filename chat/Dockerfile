FROM phpswoole/swoole:php8.2-alpine

# Create app directory
WORKDIR /var/www

# Install init system
RUN apk add --update --no-cache tini
ENTRYPOINT ["tini", "--"]

# Install missing extensions
RUN docker-php-ext-install pcntl opcache

# Bundle app source
COPY . .

# Install app dependencies
RUN composer install --no-cache --optimize-autoloader --no-dev && \
    php artisan cache:clear && \
    php artisan optimize && \
    php artisan config:cache && \
    php artisan event:cache && \
    php artisan route:cache && \
    php artisan view:cache

EXPOSE 8080
CMD [ "php", "artisan", "octane:start", "--server=swoole", "--host=0.0.0.0", "--port=8080" ]
