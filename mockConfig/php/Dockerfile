ARG PHP_VERSION="8.2.6-fpm"

FROM php:${PHP_VERSION}

ARG NODE_VERSION=15
ARG IMAGICK=true
ARG MSSQL=false
ARG MYSQLI=true

USER root

ENV ACCEPT_EULA=Y

# Install system dependencies
RUN apt-get update > /dev/null && \
	apt-get install -y --no-install-recommends \
	git \
	curl \
	libpng-dev \
	libonig-dev \
	libxml2-dev \
	libzip-dev \
	zip \
	unzip \
	libmagickwand-dev \
	libxslt1-dev \
	xfonts-75dpi \
	xfonts-base \
	libfreetype6-dev \
	libjpeg62-turbo-dev \
	libgd-dev \
	> /dev/null


# Microsoft SQL Server Prerequisites
RUN if [ "$MSSQL" = "true" ]; then \
	apt-get update > /dev/null \
	&& apt-get install -y --no-install-recommends gnupg2 > /dev/null \
	&& apt-get update > /dev/null \
	&& curl https://packages.microsoft.com/keys/microsoft.asc | apt-key add - \
	&& curl https://packages.microsoft.com/config/debian/9/prod.list \
	> /etc/apt/sources.list.d/mssql-release.list \
	&& apt-get install -y --no-install-recommends \
	locales \
	apt-transport-https \
	> /dev/null \
	&& echo "en_US.UTF-8 UTF-8" > /etc/locale.gen \
	&& locale-gen > /dev/null \
	&& apt-get update > /dev/null \
	&& apt-get -y --no-install-recommends install \
	unixodbc-dev \
	msodbcsql17 \
	> /dev/null \
	; fi

# Install Imagick
RUN if [ "$IMAGICK" = "true" ]; then pecl install imagick > /dev/null || echo "^"; fi

RUN docker-php-ext-configure gd --enable-gd --with-freetype --with-jpeg

# Install PHP extensions
RUN docker-php-ext-install \
	pdo_mysql \
	mbstring \
	exif \
	pcntl \
	bcmath \
	gd \
	soap \
	zip \
	intl \
	xsl \
	calendar \
	> /dev/null


# Install Imagick
RUN if [ "$MYSQLI" = "true" ]; then docker-php-ext-install mysqli > /dev/null || echo "^"; fi

# Set PHP ini vars
# COPY php/custom.ini /usr/local/etc/php/conf.d/custom.ini

# Install & enable Microsoft SQL Server
RUN if [ "$MSSQL" = "true" ]; then \ 
	docker-php-ext-install pdo pdo_mysql > /dev/null \
	&& pecl install sqlsrv pdo_sqlsrv > /dev/null \
	&& docker-php-ext-enable sqlsrv pdo_sqlsrv > /dev/null \
	; fi

# Enable Imagick
RUN if [ "$IMAGICK" = "true" ]; then docker-php-ext-enable imagick > /dev/null ; fi

# Get latest Composer
RUN php -r "readfile('http://getcomposer.org/installer');" | php -- --install-dir=/usr/bin/ --filename=composer > /dev/null

# Install wkhtmltopdf
RUN curl https://github.com/wkhtmltopdf/packaging/releases/download/0.12.6-1/wkhtmltox_0.12.6-1.buster_arm64.deb -L --output wkhtmltox.deb > /dev/null \
	&& dpkg -i wkhtmltox.deb > /dev/null \
	&& rm wkhtmltox.deb > /dev/null

# Install Node.js
# RUN curl --silent --location https://deb.nodesource.com/setup_${NODE_VERSION}.x | bash - > /dev/null
# RUN apt-get install --yes nodejs build-essential > /dev/null \
# 	&& npm install -g @vue/cli > /dev/null
# RUN mkdir /tmp/npm && \
# 	npm set cache --global /tmp/npm && \
# 	chmod -R 777 /tmp/npm

# RUN chmod -R 777 /home

# Clear cache
RUN apt-get clean > /dev/null && \
	rm -rf /var/lib/apt/lists/*

# Add start script
# COPY start.sh /usr/bin/start.sh



# Set working directory
# WORKDIR /var/www