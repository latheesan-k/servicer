FROM nginx:1.15.1-alpine

WORKDIR /code

RUN apk update \
    && apk add php7-phar \
    && apk add php7-mbstring \
    && apk add php7-curl \
    && apk add php7-tokenizer \
    && apk add php7-json \
    && apk add php7-phpdbg \
    && rm -rf /var/cache/apk/* /var/tmp/*/tmp/*

COPY site.conf /etc/nginx/conf.d/default.conf
