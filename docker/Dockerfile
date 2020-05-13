FROM php:7.0-apache
MAINTAINER @raulneis <raulneis@gmail.com>

ENV PATH="./vendor/bin:${PATH}"
ENV DEBIAN_FRONTEND noninteractive

RUN apt-get update \
    && apt-get -y --no-install-recommends install \
               build-essential \
               git \
               libfontconfig1 \
               libicu-dev zlib1g-dev g++ \
               libpng-dev \
               libpq-dev \
               libxext6 \
               libxml2-dev \
               libxrender1 \
               nano \
               procps \
               redis-server \
               supervisor \
               gnupg \
#               nodejs \
#    && apt-get -y autoremove
    # && apt-get clean \
    # && rm -rf /var/lib/apt/lists/* /tmp/* /var/tmp/*
    && docker-php-ext-configure pgsql -with-pgsql=/usr/local/pgsql \
    && docker-php-ext-install pdo pdo_pgsql pgsql

RUN curl -sL https://deb.nodesource.com/setup_8.x -o nodesource_setup.sh \
    && bash nodesource_setup.sh \
    && apt-get -y --no-install-recommends install nodejs \
    && rm nodesource_setup.sh

RUN npm i npm@latest -g
RUN npm install -g nodemon

RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

RUN  docker-php-ext-install gd \
                            intl \
                            pgsql \
                            pdo_pgsql \
                            xml \
                            zip \
    && pecl install apcu xdebug-2.7.0alpha1 \
    && docker-php-ext-enable apcu xdebug

COPY config/php/php.ini /usr/local/etc/php/php.ini

COPY config/supervisor/ /etc/supervisor/

COPY config/redis/redis.conf /etc/redis/redis.conf
RUN mkdir -p /var/log/redis
RUN chown redis:redis /var/log/redis -R

RUN echo "IncludeOptional /usr/local/etc/apache2/*.conf" >> /etc/apache2/apache2.conf
RUN mkdir -p /usr/local/etc/apache2
ADD config/apache2/apache2.conf /usr/local/etc/apache2/apache2.conf
ADD config/apache2/000-default.conf /etc/apache2/sites-enabled/000-default.conf


WORKDIR /var/www/html
RUN a2enmod rewrite headers
RUN usermod -u 1000 www-data

RUN chown www-data:www-data /var/www -R

RUN mkdir -p /var/backups/hcd \
    && chown www-data:www-data /var/backups/hcd

EXPOSE 80

ENV APACHE_RUN_USER    www-data
ENV APACHE_RUN_GROUP   www-data
ENV APACHE_PID_FILE    /var/run/apache2.pid
ENV APACHE_RUN_DIR     /var/run/apache2
ENV APACHE_LOCK_DIR    /var/lock/apache2
ENV APACHE_LOG_DIR     /var/log/apache2

RUN update-rc.d redis-server disable

COPY config/start.sh /usr/local/bin/start
RUN chmod +x /usr/local/bin/start
CMD ["/usr/local/bin/start"]
