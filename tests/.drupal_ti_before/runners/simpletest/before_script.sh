#!/bin/bash
# Simple script to install drupal for travis-ci running.

set -e $DRUPAL_TI_DEBUG

# Ensure the right Drupal version is installed.
drupal_ti_ensure_drupal

# Download the dev version of ctools to make PHP7 work.
cd "$DRUPAL_TI_DRUPAL_DIR"
drush --yes dl ctools-7.x-1.x-dev
