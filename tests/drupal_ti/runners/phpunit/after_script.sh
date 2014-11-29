#!/bin/bash
# @file
# PHP Unit integration - After Script step.

if [ -n "$DRUPAL_TI_COVERAGE" ]
then
	coveralls -v
fi
