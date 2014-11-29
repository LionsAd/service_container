#!/bin/bash
# @file
# Simple script to run the tests, optionally installs minimal Drupal.

set -e

# Store the module directory.
export MODULE_DIR=$(cd $(dirname $0); cd ..; pwd)

# Goto current directory.
DIR=$(dirname $0)
cd $DIR

# Do we already have a local Drupal version?
export FOUND=""
{ drush status | grep -q 'Drupal version'; } && FOUND="yes"
URI=""

if [ -z "$FOUND" ]
then
	echo "No Drupal installation found. Assuming /tmp/drupal_service_container via travis."

        # Find drupal installation in /tmp/.
        cd /tmp/drupal_service_container/drupal/

	# Point service_container into the drupal installation.
	ln -sf "$MODULE_DIR" sites/all/modules/service_container

        # Enable it to download dependencies.
	drush --yes en service_container
        URI="--uri=http://127.0.0.1:8080"
fi

drush test-run "service_container" $URI
