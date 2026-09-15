ARG HTTP_PROVIDER=fpm
ARG PHP_VERSION=8

FROM php:$PHP_VERSION-$HTTP_PROVIDER AS base

RUN apt-get update \
&& apt-get install -y zip unzip libzip-dev \
    build-essential procps net-tools \
    libfcgi-bin netcat-traditional \
    curl gettext \
    libxslt-dev \
    default-mysql-client \
&& docker-php-ext-install zip

# Install extensions
RUN docker-php-ext-install mysqli xsl



# Web stage
FROM base AS app

# Set up app home
ENV APP_HOME=/app/newton_lsa
RUN mkdir -p $APP_HOME && chown -R www-data:www-data /app
WORKDIR $APP_HOME

# Copy application code
COPY --chown=www-data:www-data . $APP_HOME