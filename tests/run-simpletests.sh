#!/bin/bash
# @file
# Simple script to run the tests, optionally installs minimal Drupal.

set -e

# Store the module directory.
export MODULE_DIR=$(cd $(dirname $0); cd ..; pwd)

# Export the PATH to include the global composer namespace.
export PATH=~/.composer/vendor/bin:$PATH

# Goto current directory.
DIR=$(dirname $0)
cd $DIR

# Do we already have a local Drupal version?
export FOUND=""
{ drush status | grep -q 'Drupal version'; } && FOUND="yes"

if [ -z "$FOUND" ]
then
	echo "No Drupal installation found. Installing via git."

        # Install in /tmp directory to prevent endless recursion.
        cd /tmp/

	# Install Drupal 7.x
	git clone --branch 7.x --depth 1 http://git.drupal.org/project/drupal.git
	cd drupal

	# Point service_container into the drupal installation.
	ln -sf "$MODULE_DIR" sites/all/modules/service_container

	# Install it and run the tests.
	drush --yes site-install minimal --db-url="sqlite://tmp/drupal_database"

	drush --yes en simpletest
	drush --yes en service_container
fi

drush test-run "service_container"
