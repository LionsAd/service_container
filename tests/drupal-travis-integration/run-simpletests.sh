#!/bin/bash
# @file
# Simple script to run the tests via travis-ci.

set -e

cd /tmp/drupal_travis/drupal/
drush test-run "$@"
