FROM debian:jessie

ARG DEBIAN_FRONTEND=noninteractive

# Repository updates and upgrades
RUN apt-get update && apt-get upgrade -y && \
    apt-get -yq install \
	wget \
	curl \
	locales \
	apt-transport-https \
	lsb-release \
	ca-certificates

# Timezone and locales
RUN echo "Europe/Paris" > /etc/timezone && \
    dpkg-reconfigure -f noninteractive tzdata
RUN echo "fr_FR.UTF-8 UTF-8" > /etc/locale.gen && \
    locale-gen fr_FR.UTF-8 && \
    dpkg-reconfigure locales && \
    /usr/sbin/update-locale LANG=fr_FR.UTF-8
ENV LC_ALL fr_FR.UTF-8

# deb.sury.org
RUN wget -O /etc/apt/trusted.gpg.d/php.gpg https://packages.sury.org/php/apt.gpg
RUN echo "deb https://packages.sury.org/php/ jessie main" > /etc/apt/sources.list.d/php.list
RUN apt-get update

# PHP 7 installation
RUN apt-get -yq install \
	php7.1 \
	php7.1-cli \
	libapache2-mod-php7.1 \
	php7.1-mysql \
	php7.1-gd \
	php7.1-mbstring \
	php7.1-curl \
	php7.1-xmlwriter \
	php7.1-zip \
	php7.1-json \
	php7.1-soap \
	php7.1-xdebug

# PHP configuration
RUN sed -i 's/\;date\.timezone\ \=/date\.timezone\ \=\ Europe\/Paris/g' /etc/php/7.1/cli/php.ini
RUN sed -i 's/\;date\.timezone\ \=/date\.timezone\ \=\ Europe\/Paris/g' /etc/php/7.1/apache2/php.ini

# Composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# ssmtp
RUN apt-get install -yq ssmtp && \
    echo "FromLineOverride=YES" >> /etc/ssmtp/ssmtp.conf && \
    sed -i 's/mailhub=mail//g' /etc/ssmtp/ssmtp.conf && \
    echo "mailhub="$(/sbin/ip route | awk '/default/ { print $3 }') >> /etc/ssmtp/ssmtp.conf && \
    sed -i 's/\;sendmail_path\ \=/sendmail_path\ \=\ \"\/usr\/sbin\/ssmtp\ \-t\"/g' /etc/php/7.1/apache2/php.ini

# Cleanup
RUN rm -rf /var/lib/apt/lists/*

# Apache configuration
RUN sed -i 's/DocumentRoot\ \/var\/www\/html/DocumentRoot\ \/var\/www/g' /etc/apache2/sites-enabled/000-default.conf
ADD 001-vhosts.conf /etc/apache2/sites-available/
RUN echo "ServerName localhost" >> /etc/apache2/apache2.conf && \
    a2enmod rewrite vhost_alias && \
    a2ensite 001-vhosts

# Start script
EXPOSE 80
ADD ./start.sh /usr/bin/start
RUN chmod 0755 /usr/bin/start
CMD ["bash", "start"]
