FROM ubuntu:latest
LABEL maintainer="psmever <psmever@gmail.com>"

ENV DEBIAN_FRONTEND noninteractive
ENV LC_ALL=C.UTF-8

ARG OS_LOCALE

ENV TZ=Asia/Seoul

EXPOSE 80
EXPOSE 43380
EXPOSE 9000

ADD ./files/entrypoint.sh /usr/local/bin/entrypoint.sh
ADD ./files/start.sh /usr/local/bin/start.sh
RUN chmod +x /usr/local/bin/start.sh

RUN apt-get update

# System.. apt install
RUN apt-get install -y \
    apt-utils \
    language-pack-ko \
    tzdata \
    net-tools \
    curl \
    vim \
    iputils-ping \
    unzip

RUN ln -snf /usr/share/zoneinfo/$TZ /etc/localtime && echo $TZ > /etc/timezone
RUN locale-gen ko_KR.UTF-8
RUN localedef -f UTF-8 -i ko_KR ko_KR.UTF-8

RUN apt-get install -y software-properties-common
RUN add-apt-repository ppa:ondrej/php
RUN apt-get update

# Developer apt install
RUN apt-get install -y \
    git \
    mariadb-client \
    nginx \
    php8.2 \
    php8.2-common \
    php8.2-cli \
    libphp8.2-embed \
    php8.2-bz2 \
    php8.2-mbstring \
    php8.2-zip \
    php8.2-curl \
    php8.2-xml \
    php8.2-gd \
    php8.2-fpm \
    php8.2-sqlite3 \
    php8.2-mysql

RUN apt-get clean && rm -rf /var/lib/apt/lists/* /tmp/* /var/tmp/*

RUN /bin/bash /usr/local/bin/entrypoint.sh

RUN curl -s https://getcomposer.org/installer | php
RUN mv composer.phar /usr/local/bin/composer
RUN composer about

RUN chown www-data:www-data /var/www
ADD ./files/nginx_default /etc/nginx/sites-available/default
