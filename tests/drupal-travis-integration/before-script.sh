#!/bin/bash
# Simple script to install drupal for travis-ci running.

set -e
set -x

if [ $# -lt 4 ]
then
  echo "Usage: $0 <module-dir> <module-name> <db-name> <db-url>" 1>&2
  exit 1
fi

export MODULE_DIR=$1
export MODULE_NAME=$2
export DB=$3
export DB_URL=$4

# Change to /tmp
cd /tmp

# Install drupal
mysql -e "create database $DB"
php -d sendmail_path=$(which true) ~/.composer/vendor/bin/drush.php --yes core-quick-drupal --profile=testing --no-server --db-url="$DB_URL" --enable=simpletest drupal_travis
cd drupal_travis/drupal

# Point service_container into the drupal installation.
ln -sf "$MODULE_DIR" sites/all/modules/$MODULE_NAME

# Enable it to download dependencies.
drush --yes en "$MODULE_NAME"

# start a web server on port 8080, run in the background; wait for initialization
drush runserver "127.0.0.1:8080" &
until netstat -an 2>/dev/null | grep '8080.*LISTEN'; do true; done
