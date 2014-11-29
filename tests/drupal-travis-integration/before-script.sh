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

# Create database and Install drupal.
mysql -e "create database $DB"
php -d sendmail_path=$(which true) ~/.composer/vendor/bin/drush.php --yes core-quick-drupal --profile=testing --no-server --db-url="$DB_URL" drupal_travis
cd drupal_travis/drupal

# Enable simpletest manually - sendmail fails on HHVM.
drush --yes en "simpletest"

# Point service_container into the drupal installation.
ln -sf "$MODULE_DIR" sites/all/modules/$MODULE_NAME

# Enable it to download dependencies.
drush --yes en "$MODULE_NAME"

# start a web server on port 8080, run in the background; wait for initialization
drush runserver "127.0.0.1:8080" &
until netstat -an 2>/dev/null | grep -q '8080.*LISTEN'
do
	sleep 1
done
