FROM yiisoftware/yii2-php:7.3-apache

RUN a2enmod rewrite
RUN a2enmod ssl
RUN a2enmod dav
RUN a2enmod dav_fs

RUN apt-get -qq update && apt-get -qqy install  \
        git \
        locales \
        libxslt-dev \
        libssh2-1-dev \
        cmake \
        libssl-dev \
    && apt-get autoremove -y \
    && apt-get clean all

RUN cd /tmp && \
    git clone https://github.com/php/pecl-networking-ssh2.git && \
    cd /tmp/pecl-networking-ssh2/ && \
    .travis/build.sh && \
    docker-php-ext-enable ssh2 && \
    rm -rf /tmp/pecl-networking-ssh2/

RUN pecl uninstall xdebug; yes | pecl install xdebug-2.9.8; \
    docker-php-ext-install xsl
# set ru_RU locale
RUN sed -i -e 's/# ru_RU.UTF-8 UTF-8/ru_RU.UTF-8 UTF-8/' /etc/locale.gen && \
    sed -i -e 's/# en_US.UTF-8 UTF-8/en_US.UTF-8 UTF-8/' /etc/locale.gen && \
    locale-gen && \
    update-locale LANG=ru_RU.UTF-8

RUN cd /tmp && \
    git clone --branch=openssl_1_1_0 https://github.com/gost-engine/engine.git && \
    cd /tmp/engine && \
    sed -i 's|printf("GOST engine already loaded\\n");|goto end;|' gost_eng.c && \
    cmake . && \
    make && \
    cp /tmp/engine/bin/gost.so /usr/lib/x86_64-linux-gnu/engines-1.1

RUN composer self-update --1

COPY openssl.cnf /etc/ssl/openssl.cnf
COPY server.crt /etc/apache2/ssl/server.crt
COPY server.key /etc/apache2/ssl/server.key

ENV LANG ru_RU.utf8
