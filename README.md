# Apache - PHP for web projects with Docker

This package will give you a full Apache-PHP stack for your web projects. The main difference between this project and others is that it will use the Apache module vhost_alias to handle your projects hostnames only by creating folders.

## Prerequisites

This is required to make the package work as is without any modification :

  * Docker host must be based on Linux
  * Having ```docker-compose``` installed :  [https://docs.docker.com/compose/install/](https://docs.docker.com/compose/install/)
  * ```/var/www``` must exists on host and must have rw permissions for the current user
  * ```docker``` must be executable by the current user : ```sudo usermod -aG docker [username]```
  * Port 80 must be free on host
  * Not required but needed to allow PHP to send emails : host must have a working stmp service (postfix, ...)

## Details

  * [Debian jessie](https://index.docker.io/_/debian/)
  * [deb.sury.org](https://deb.sury.org/) repository
  * Apache 2.4 (rewrite, vhost_alias)
  * PHP 7.1 (cli, mysql, gd, mbstring, curl, xmlwriter, zip, json, soap, xdebug)
  * ssmtp

## Quick start

Start :

- interactive : ```docker-compose up```
- daemon : ```docker-compose up -d ```

Stop :

- ```docker-compose stop```

## Usage

### Creating a new virtual host

When the container is running you can create a new virtual host like this :

```
cd /var/www
mkdir -p mywebsite.com/www
echo "<?php echo $_SERVER['http_host'];" > mywebsite.com/www/index.php
```

After that you must map the hostname to the right IP into ```/etc/hosts``` (or you can use a private DNS server like bind9 to set a wildcard \*.dev for example) :

```
127.0.0.1    www.mywebsite.com.dev
```

You can go now on ```http://www.mywebsite.com.dev``` and see the result. If everything works you will see the hostname.

You can already create hostnames like mail.mywebsite.com.dev if you create a folder called mail into the folder mywebsite.com

### php-cli

When the container is running you can use php in CLI mode.

Important : this will only work into the folder /var/www and his subfolders

For that - as root - you can copy the file ```php``` you will find with this package into ```/usr/bin```, don't forget to make it executable : ```chmod +x /usr/bin/php```.

After that you can test it : ```php -v```

## Locales

By default, container locales and timezone are sets to "```fr_FR.UTF-8```" (sorry, i'm french...) and "```Europe/Paris```", but you can change it very easily in dockerfile
