#!/bin/sh

PHP_TRAVIS_ENV=`printenv TRAVIS_PHP_VERSION`
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
    mv phpunit.phar /usr/bin/phpunit
    chmod 755 phpunit
    PHPUNIT="/usr/bin/phpunit"
    echo $PHPUNIT
    export PHPUNIT="${PHPUNIT}"
fi
