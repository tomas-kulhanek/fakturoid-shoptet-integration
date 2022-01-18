FROM composer:2 AS composer

FROM ghcr.io/tomas-kulhanek/docker-application:v1.0.0 AS builder
WORKDIR /var/www
RUN apt-get -y --no-install-recommends update && \
    apt-get -y --no-install-recommends install git && \
    apt-get clean && \
    rm -rf /var/lib/apt/lists/* /tmp/* /var/tmp/* /usr/share/doc/* /var/cache/apt/lists

COPY --from=composer /usr/bin/composer /usr/bin/composer
COPY ./composer.* ./
RUN composer install --no-dev --no-progress --no-interaction --no-scripts

FROM node:14-alpine AS nodeModules
WORKDIR /usr/src/app
COPY package.json yarn.lock webpack.config.js ./
COPY assets/ ./assets/
RUN apk add --no-cache git
RUN yarn install
RUN yarn encore production

FROM ghcr.io/tomas-kulhanek/docker-application:v1.0.0 AS development
WORKDIR /var/www
RUN apt-get -y --no-install-recommends update && \
    apt-get -y --no-install-recommends upgrade && \
    apt-get -y --no-install-recommends install git vim curl php8.0-xdebug && \
    apt-get clean && \
    rm -rf /var/lib/apt/lists/* /tmp/* /var/tmp/* /usr/share/doc/* /var/cache/apt/lists

COPY --from=composer /usr/bin/composer /usr/bin/composer
COPY --from=nodeModules /usr/src/app/public/build /var/www/public/build


FROM ghcr.io/tomas-kulhanek/docker-application:v1.0.0
WORKDIR /var/www
RUN apt-get -y --no-install-recommends update && \
    apt-get -y --no-install-recommends upgrade && \
    apt-get -y --no-install-recommends install curl && \
    apt-get clean && \
    rm -rf /var/lib/apt/lists/* /tmp/* /var/tmp/* /usr/share/doc/* /var/cache/apt/lists
COPY --from=builder /var/www .
COPY --from=nodeModules /usr/src/app/public/build /var/www/public/build
COPY . ./
RUN chown -R www-data:www-data var
