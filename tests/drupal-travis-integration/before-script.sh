#!/bin/bash
# Simple script to install drupal for travis-ci running.

set -e
set -x

# Return if we should ignore this call.
[ -n "$IGNORE_DRUPAL_TRAVIS_INTEGRATION" ] && exit 0

if [ $# -lt 3 ]
then
  echo "Usage: $0 <module-name> <db-name> <db-url>" 1>&2
  exit 1
fi

export MODULE_NAME=$1
export DB=$2
export DB_URL=$3

# Find absolute path to module.
cd "$TRAVIS_BUILD_DIR"
MODULE_DIR=$(pwd)
cd ..

# Determine php.ini per https://github.com/travis-ci/travis-ci/issues/2523.
PHP_VERSION=`phpenv version-name`
if [ "$PHP_VERSION" = "hhvm" ]
then
  PHPINI=/etc/hhvm/php.ini
else
  PHPINI="~/.phpenv/versions/$VERSION/etc/php.ini"
fi

# Set sendmail so drush doesn't throw an error during site install.
export TRUE_SCRIPT=$(which true)
echo "sendmail_path='$TRUE_SCRIPT'" >> "$PHPINI"

# Create database and install Drupal.
mysql -e "create database $DB"
drush --yes core-quick-drupal --profile=testing --no-server --db-url="$DB_URL" --enable="simpletest" drupal_travis
cd drupal_travis/drupal

# Point service_container into the drupal installation.
ln -sf "$MODULE_DIR" "sites/all/modules/$MODULE_NAME"

# Enable it to download dependencies.
drush --yes en "$MODULE_NAME"

# start a web server on port 8080, run in the background; wait for initialization
drush runserver "127.0.0.1:8080" &
until netstat -an 2>/dev/null | grep -q '8080.*LISTEN'
do
	sleep 1
done
