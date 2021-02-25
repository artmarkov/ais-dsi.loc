FROM yiisoftware/yii2-php:7.3-apache

RUN a2enmod rewrite
RUN a2enmod ssl
RUN a2enmod dav
RUN a2enmod dav_fs

RUN apt-get -qq update && apt-get -qqy install  \
        locales \
        libxslt-dev \
        libssh2-1-dev \
    && apt-get autoremove -y \
    && apt-get clean all

RUN cd /tmp && \
    git clone https://git.php.net/repository/pecl/networking/ssh2.git && \
    cd /tmp/ssh2/ && \
    .travis/build.sh && \
    docker-php-ext-enable ssh2

RUN yes | pecl install xdebug; \
    docker-php-ext-enable xdebug; \
    docker-php-ext-install xsl

# set ru_RU locale
RUN sed -i -e 's/# ru_RU.UTF-8 UTF-8/ru_RU.UTF-8 UTF-8/' /etc/locale.gen && \
    sed -i -e 's/# en_US.UTF-8 UTF-8/en_US.UTF-8 UTF-8/' /etc/locale.gen && \
    locale-gen && \
    update-locale LANG=ru_RU.UTF-8

COPY server.crt /etc/apache2/ssl/server.crt
COPY server.key /etc/apache2/ssl/server.key

ENV LANG ru_RU.utf8
