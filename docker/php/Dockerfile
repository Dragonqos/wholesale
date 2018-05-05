FROM lyberteam/php-fpm-7.1

RUN docker-php-ext-install pcntl

RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

CMD ["php-fpm"]