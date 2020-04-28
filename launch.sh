#!/bin/bash
if [ "$#" -ne 1 ]; then
    echo "Usage: $0 <port number>"
    exit 1
fi

#check php installation
dpkg -s php7.2 &> /dev/null
if [ $? -ne 0 ]; then
    echo "Will install php"
    apt update && apt install php7.2 -y
fi

#check php-xml installation
dpkg -s php7.2-xml &> /dev/null
if [ $? -ne 0 ]; then
    echo "Will install php-xml"
    apt update && apt install php7.2-xml -y
fi

#check php-gd installation
dpkg -s php7.2-gd &> /dev/null
if [ $? -ne 0 ]; then
    echo "Will install php-gd"
    apt update && apt install php7.2-gd -y
fi

PORT=$1
php -S 0.0.0.0:$PORT
