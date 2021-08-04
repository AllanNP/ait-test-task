FROM php:8-fpm

RUN apt-get update \
  && apt-get install -y \
		curl \
		vim \
		libfreetype6-dev \
		libjpeg62-turbo-dev \
		libmcrypt-dev \
		libpng-dev \
		zlib1g-dev \
		libxml2-dev \
		libzip-dev \
		libonig-dev \
		libxslt-dev \
		graphviz \
		libcurl4-openssl-dev \
		pkg-config \
		libpq-dev \
		iputils-ping \
		wget \
		git \
    && docker-php-ext-install -j$(nproc) iconv mbstring zip intl sockets exif xsl \
    && docker-php-ext-install -j$(nproc) pgsql \
    && docker-php-ext-install -j$(nproc) pdo_pgsql \
	&& docker-php-ext-configure gd --with-freetype --with-jpeg \
	&& docker-php-ext-install -j$(nproc) gd


RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

RUN curl -sS https://get.symfony.com/cli/installer | bash
RUN mv /root/.symfony/bin/symfony /usr/local/bin/symfony

COPY php.ini /usr/local/etc/php/conf.d/custom-config.ini

WORKDIR /var/www/

RUN chmod 755 /var/www/

CMD ["php-fpm"]

