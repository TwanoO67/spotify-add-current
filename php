#!/bin/bash

FILE=$1

#if [ -n "$FILE" ] && [ -f $FILE ];then
#    FILE=$(readlink -e $FILE)
#fi

docker exec -it apache-php-dev php /var/www/$FILE
