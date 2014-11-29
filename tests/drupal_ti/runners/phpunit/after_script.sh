#!/bin/bash
# @file
# PHP Unit integration - After Script step.

set -e -x

if [ -n "$DRUPAL_TI_COVERAGE" ]
then
	coveralls -v
fi
