FROM ubuntu:20.04

ENV timezone=America/Sao_Paulo

RUN apt-get update && apt-get upgrade -y && \
    ln -snf /usr/share/zoneinfo/${timezone} /etc/localtime && echo ${timezone} > /etc/timezone && \
    apt-get install -y apache2 && \
    apt install -y software-properties-common && \
    add-apt-repository --yes ppa:ondrej/php && \
    apt-get update && \
    apt-get install php7.4 -y && \
    apt-get install -y php7.4-common && \
    apt-get install -y php7.4-mysql && \
    apt-get install -y php7.4-xml && \
    apt-get install -y php7.4-xmlrpc && \
    apt-get install -y php7.4-curl && \
    apt-get install -y php7.4-gd && \
    apt-get install -y php7.4-imagick && \
    apt-get install -y php7.4-cli && \
    apt-get install -y php7.4-dev && \
    apt-get install -y php7.4-imap && \
    apt-get install -y php7.4-mbstring && \
    apt-get install -y php7.4-opcache && \
    apt-get install -y php7.4-soap && \
    apt-get install -y php7.4-zip && \
    apt-get install -y php7.4-intl && \
    apt-get install -y php7.4-xdebug && \
    apt-get install -y git && \
    php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');" && php composer-setup.php && rm composer-setup.php && mv composer.phar /usr/local/bin/composer && chmod a+x /usr/local/bin/composer


COPY ./docker/server/apache2.conf /etc/apache2/apache2.conf

EXPOSE 80

WORKDIR /var/www/html

ENTRYPOINT a2enmod rewrite && service apache2 start && /bin/bash

CMD ["true"]
