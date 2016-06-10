#!/bin/sh

PHP_TRAVIS_ENV="7.0.1"
PHP_VERSION="7"
HOME=`printenv HOME`

if [ -z $HOME ]; then
    HOME=`pwd`
fi

PHPUNIT_DIR="${HOME}/phpunit/bin"
mkdir -p $PHPUNIT_DIR


if [ $PHP_TRAVIS_ENV \> $PHP_VERSION ]; then
    cd $PHPUNIT_DIR
    wget https://phar.phpunit.de/phpunit.phar
    mv phpunit.phar phpunit
    chmod 755 phpunit
    PHPUNIT="${PHPUNIT_DIR}/phpunit"
    echo $PHPUNIT
    export PHPUNIT="${PHPUNIT}"
fi
