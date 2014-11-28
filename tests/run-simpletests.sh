#!/bin/bash
# @file
# Simple script to install drupal run the tests.

set -e

DIR=$(dirname $0)
cd $DIR
# Install Drupal 7.x
git clone --branch 7.x --depth 1 http://git.drupal.org/project/drupal.git
cd drupal

# Install the module dependencies.
git clone --branch 7.x-1.x --depth 1 http://git.drupal.org/project/ctools.git sites/all/modules/ctools
git clone --branch 7.x-1.x --depth 1 http://git.drupal.org/project/registry_autoload.git sites/all/modules/registry_autoload

# Point service_container into the drupal installation.
ln -s $DIR sites/all/modules/service_container

alias drush="php ~/.composer/vendor/bin/drush"

# Install it and run the tests.
drush --yes site-install minimal --db-url="sqlite://tmp/drupal_database"

drush --yes en simpletest
drush --yes en service_container

drush test-run "service_container" --debug
