#!/bin/bash
# @file
# PHP Unit integration - Install step.

set -e $DRUPAL_TI_DEBUG

composer install --no-interaction --prefer-source --dev

if [ -n "$DRUPAL_TI_COVERAGE" ]
then
	composer global require "$DRUPAL_TI_COVERAGE"
fi
