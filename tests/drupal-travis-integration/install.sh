#!/bin/bash
# Simple script to install dependencies for travis-ci running.

set -e
set -v

# install php packages required for running a web server from drush on php 5.3
PHP_VERSION=$(phpenv version-name)
if [ "$PHP_VERSION" = "5.3" ]
then
	sudo apt-get install -y --force-yes php5-cgi php5-mysql
fi

# add composer's global bin directory to the path
# see: https://github.com/drush-ops/drush#install---composer
export PATH="$HOME/.composer/vendor/bin:$PATH"

# install drush globally
composer global require drush/drush:dev-master --prefer-source
