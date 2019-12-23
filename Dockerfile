FROM 922969856207.dkr.ecr.ap-southeast-1.amazonaws.com/giaingay-apache-php:1.0
MAINTAINER LienLQ <lienlq3@topica.edu.vn>
ENV DEBIAN_FRONTEND noninteractive

RUN apt-get update

WORKDIR /app/

EXPOSE ${PORT}

ENV APACHE_LOG_DIR /var/log/apache2
ENV APACHE_LOCK_DIR /var/lock/apache2
ENV APACHE_PID_FILE /var/run/apache2.pid

# Expose apache.

# Update the default apache site with the config we created.
ADD Docker/apache-config.conf /etc/apache2/sites-enabled/000-default.conf
ADD Docker/apache2-port.conf /etc/apache2/ports.conf
ADD Docker/apache2.conf /etc/apache2/apache2.conf

COPY composer.json /app/composer.json
COPY composer.lock /app/composer.lock

RUN composer install --no-scripts

COPY . /app/

RUN composer dump-autoload --no-scripts

#Install pm2
RUN curl -sL https://deb.nodesource.com/setup_12.x | bash -
RUN apt-get install -y nodejs
RUN npm install pm2 -g -y

ENTRYPOINT [ "bash","/app/Docker/docker-entrypoint.sh" ]

CMD /usr/sbin/apache2ctl -D FOREGROUND