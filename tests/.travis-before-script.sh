#!/bin/bash

set -e $DRUPAL_TI_DEBUG

# Ensure the right Drupal version is installed.
# Note: This function is re-entrant.
drupal_ti_ensure_drupal

# Add needed dependencies.
cd "$DRUPAL_TI_DRUPAL_DIR"

# These variables come from environments/drupal-*.sh
mkdir -p "$DRUPAL_TI_MODULES_PATH"
cd "$DRUPAL_TI_MODULES_PATH"

# Download service_container_libraries
git clone --depth 1 --branch master https://github.com/LionsAd/service_container_libraries.git

# Enable the libraries module
drush -y en service_container_libraries
