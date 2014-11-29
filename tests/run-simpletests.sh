#!/bin/bash
# @file
# Simple script to run the tests, optionally installs minimal Drupal.

set -e

# Goto current directory.
DIR=$(dirname $0)
cd $DIR

# Do we already have a local Drupal version?
export FOUND=""
{ drush status | grep -q 'Drupal version'; } && FOUND="yes"

if [ -z "$FOUND" ]
then
	echo "No Drupal installation found. Assuming /tmp/drupal_service_container via travis-ci."

        # Find drupal installation in /tmp/.
        cd /tmp/drupal_service_container/drupal/
fi

drush test-run "service_container" "$@"
