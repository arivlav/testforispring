#Загрузить базовый образ Ubuntu 20.04
FROM ubuntu:20.04

ENV TZ=Europe/Moscow
RUN ln -snf /usr/share/zoneinfo/$TZ /etc/localtime && echo $TZ > /etc/timezone

#Обновить программный репозиторий Ubuntu
RUN apt-get update
RUN apt-get -y upgrade

#Установить nginx, php-fpm, mysql
RUN apt-get -y install nginx php7.4-fpm mysql-server

#Определение переменных среды
ENV nginx_vhost /etc/nginx/sites-available/default
ENV php_conf /etc/php/7.4/fpm/php.ini
ENV nginx_conf /etc/nginx/nginx.conf
#ENV mysql_conf /etc/mysql
#ENV MYSQL_ROOT_PASSWORD=mypassword
#            - MYSQL_DATABASE=magento2
#            - MYSQL_USER=magento2

#Конфигугация виртуального хоста nginx для работы с php-fpm
COPY nginx.conf ${nginx_vhost}
RUN sed -i -e 's/;cgi.fix_pathinfo=1/cgi.fix_pathinfo=0/g' ${php_conf} && \
echo "\ndaemon off;" >> ${nginx_conf}

RUN mkdir -p /run/php && \
chown -R www-data:www-data /var/www/html && \
chown -R www-data:www-data /run/php

#Конфигурация тома
VOLUME ["/etc/nginx/sites-enabled", "/etc/nginx/certs", "/etc/nginx/conf.d", "/var/log/nginx", "/var/www/html"]


#Порты для nginx
EXPOSE 80 443
